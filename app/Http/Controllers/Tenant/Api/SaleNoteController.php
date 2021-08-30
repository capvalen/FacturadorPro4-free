<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Models\Tenant\Configuration;
use Exception;
use Mpdf\Mpdf;
use Carbon\Carbon;
use Mpdf\HTMLParserMode;
use App\Models\Tenant\Item;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Tenant\Series;
use App\Models\Tenant\Company;
// use App\Models\Tenant\Warehouse;
use Mpdf\Config\FontVariables;
use App\CoreFacturalo\Template;
use App\Models\Tenant\SaleNote;
use App\CoreFacturalo\Facturalo;
use App\Models\Tenant\StateType;
use Modules\Item\Models\ItemLot;
use Mpdf\Config\ConfigVariables;
use App\Mail\Tenant\SaleNoteEmail;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant\SaleNoteItem;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Tenant\SaleNoteRequest;
use App\Http\Resources\Tenant\SaleNoteCollection;
use App\CoreFacturalo\Helpers\Number\NumberLetter;
use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\CoreFacturalo\Requests\Inputs\Common\PersonInput;
use App\CoreFacturalo\Requests\Inputs\Common\EstablishmentInput;

class SaleNoteController extends Controller
{
	use StorageDocument;

	protected $sale_note;

	protected $company;

	public function lists()
	{
		$record = SaleNote::orderBy('series', 'desc')->orderBy('number', 'desc')->take(50)->get();
		$records = new SaleNoteCollection($record);

		return $records;
	}

	public function store(SaleNoteRequest $request)
	{
		DB::connection('tenant')->transaction(function () use ($request) {
			$request['establishment_id'] = $request['establishment_id'] ? $request['establishment_id'] : auth()->user()->establishment_id;

			$data = $this->mergeData($request);

			$this->sale_note = SaleNote::updateOrCreate(
				['id' => $request->input('id')],
				$data
			);

			$this->sale_note->payments()->delete();

			foreach ($data['items'] as $row) {
				$item_id = isset($row['id']) ? $row['id'] : null;
				$sale_note_item = SaleNoteItem::firstOrNew(['id' => $item_id]);

				if (isset($row['item']['lots'])) {
					$row['item']['lots'] = isset($row['lots']) ? $row['lots'] : $row['item']['lots'];
				}

				$sale_note_item->fill($row);
				$sale_note_item->sale_note_id = $this->sale_note->id;
				$sale_note_item->save();

				if (isset($row['lots'])) {
					foreach ($row['lots'] as $lot) {
						$record_lot = ItemLot::findOrFail($lot['id']);
						$record_lot->has_sale = true;
						$record_lot->update();
					}
				}
			}
			//pagos
			/*foreach ($data['payments'] as $row) {
				$this->sale_note->payments()->create($row);
			}*/

			$this->setFilename();
			$this->createPdf($this->sale_note, 'a4', $this->sale_note->filename);
		});

		return [
			'success' => true,
			'data'    => [
				'id'     => $this->sale_note->id,
				'number' => $this->sale_note->number_full,
			],
		];
	}

	public function mergeData($inputs)
	{
		$this->company = Company::active();

		$type_period = $inputs['type_period'];
		$quantity_period = $inputs['quantity_period'];
		$d_of_issue = new Carbon($inputs['date_of_issue']);
		$automatic_date_of_issue = null;

		if ($type_period && $quantity_period > 0) {
			$add_period_date = ($type_period == 'month') ? $d_of_issue->addMonths($quantity_period) : $d_of_issue->addYears($quantity_period);
			$automatic_date_of_issue = $add_period_date->format('Y-m-d');
		}

		$data_series = $this->getDataSeries($inputs['series_id'], $inputs['id'], $inputs['number']);

		$values = [
			'automatic_date_of_issue' => $automatic_date_of_issue,
			'user_id'                 => auth()->id(),
			'external_id'             => Str::uuid()->toString(),
			'customer'                => PersonInput::set($inputs['customer_id']),
			'establishment'           => EstablishmentInput::set($inputs['establishment_id']),
			'soap_type_id'            => $this->company->soap_type_id,
			'state_type_id'           => '01',
			'series'                  => $data_series['series'],
			'number'                  => $data_series['number']
		];

		$inputs->merge($values);

		return $inputs->all();
	}

	private function getDataSeries($series_id, $id, $number)
	{
		$series = Series::find($series_id)->number;

		if (!$id) {
			$sale_note = SaleNote::select('number')->where('soap_type_id', $this->company->soap_type_id)
								->where('series', $series)
								->orderBy('number', 'desc')
								->first();

			$number = ($sale_note) ? $sale_note->number + 1 : 1;
		}

		return [
			'series' => $series,
			'number' => $number,
		];
	}

	private function setFilename()
	{
		$name = [$this->sale_note->prefix, $this->sale_note->id, date('Ymd')];
		$this->sale_note->filename = join('-', $name);
		$this->sale_note->save();
	}

	public function toPrint($external_id, $format)
	{
		$sale_note = SaleNote::where('external_id', $external_id)->first();

		if (!$sale_note) {
			throw new Exception("El código {$external_id} es inválido, no se encontro la nota de venta relacionada");
		}

		$this->reloadPDF($sale_note, $format, $sale_note->filename);
		$temp = tempnam(sys_get_temp_dir(), 'sale_note');

		file_put_contents($temp, $this->getStorage($sale_note->filename, 'sale_note'));

		return response()->file($temp);
	}

	private function reloadPDF($sale_note, $format, $filename)
	{
		$this->createPdf($sale_note, $format, $filename);
	}

	public function createPdf($sale_note = null, $format_pdf = null, $filename = null)
	{
		$template = new Template();
		$pdf = new Mpdf();

		$this->company = ($this->company != null) ? $this->company : Company::active();
		$this->document = ($sale_note != null) ? $sale_note : $this->sale_note;

		$base_template = config('tenant.pdf_template');

		$html = $template->pdf($base_template, 'sale_note', $this->company, $this->document, $format_pdf);

		if (($format_pdf === 'ticket') or ($format_pdf === 'ticket_58')) {
			$width = ($format_pdf === 'ticket_58') ? 56 : 78;
			if (config('tenant.enabled_template_ticket_80')) {
				$width = 76;
			}

			$company_logo = ($this->company->logo) ? 40 : 0;
			$company_name = (strlen($this->company->name) / 20) * 10;
			$company_address = (strlen($this->document->establishment->address) / 30) * 10;
			$company_number = $this->document->establishment->telephone != '' ? '10' : '0';
			$customer_name = strlen($this->document->customer->name) > '25' ? '10' : '0';
			$customer_address = (strlen($this->document->customer->address) / 200) * 10;
			$p_order = $this->document->purchase_order != '' ? '10' : '0';

			$total_exportation = $this->document->total_exportation != '' ? '10' : '0';
			$total_free = $this->document->total_free != '' ? '10' : '0';
			$total_unaffected = $this->document->total_unaffected != '' ? '10' : '0';
			$total_exonerated = $this->document->total_exonerated != '' ? '10' : '0';
			$total_taxed = $this->document->total_taxed != '' ? '10' : '0';
			$quantity_rows = count($this->document->items);
			$payments = $this->document->payments()->count() * 2;

			$extra_by_item_description = 0;
			$discount_global = 0;
			foreach ($this->document->items as $it) {
				if (strlen($it->item->description) > 100) {
					$extra_by_item_description += 24;
				}
				if ($it->discounts) {
					$discount_global = $discount_global + 1;
				}
			}
			$legends = $this->document->legends != '' ? '10' : '0';

			$pdf = new Mpdf([
				'mode'   => 'utf-8',
				'format' => [
					$width,
					40 +
					(($quantity_rows * 8) + $extra_by_item_description) +
					($discount_global * 3) +
					$company_logo +
					$payments +
					$company_name +
					$company_address +
					$company_number +
					$customer_name +
					$customer_address +
					$p_order +
					$legends +
					$total_exportation +
					$total_free +
					$total_unaffected +
					$total_exonerated +
					$total_taxed],
				'margin_top'    => 0,
				'margin_right'  => 2,
				'margin_bottom' => 0,
				'margin_left'   => 2
			]);
		} elseif ($format_pdf === 'a5') {
			$company_name = (strlen($this->company->name) / 20) * 10;
			$company_address = (strlen($this->document->establishment->address) / 30) * 10;
			$company_number = $this->document->establishment->telephone != '' ? '10' : '0';
			$customer_name = strlen($this->document->customer->name) > '25' ? '10' : '0';
			$customer_address = (strlen($this->document->customer->address) / 200) * 10;
			$p_order = $this->document->purchase_order != '' ? '10' : '0';

			$total_exportation = $this->document->total_exportation != '' ? '10' : '0';
			$total_free = $this->document->total_free != '' ? '10' : '0';
			$total_unaffected = $this->document->total_unaffected != '' ? '10' : '0';
			$total_exonerated = $this->document->total_exonerated != '' ? '10' : '0';
			$total_taxed = $this->document->total_taxed != '' ? '10' : '0';
			$quantity_rows = count($this->document->items);
			$discount_global = 0;
			foreach ($this->document->items as $it) {
				if ($it->discounts) {
					$discount_global = $discount_global + 1;
				}
			}
			$legends = $this->document->legends != '' ? '10' : '0';

			$alto = ($quantity_rows * 8) +
					($discount_global * 3) +
					$company_name +
					$company_address +
					$company_number +
					$customer_name +
					$customer_address +
					$p_order +
					$legends +
					$total_exportation +
					$total_free +
					$total_unaffected +
					$total_exonerated +
					$total_taxed;
			$diferencia = 148 - (float)$alto;

			$pdf = new Mpdf([
				'mode'   => 'utf-8',
				'format' => [
					210,
					$diferencia + $alto
				],
				'margin_top'    => 2,
				'margin_right'  => 5,
				'margin_bottom' => 0,
				'margin_left'   => 5
			]);
		} else {
			$pdf_font_regular = config('tenant.pdf_name_regular');
			$pdf_font_bold = config('tenant.pdf_name_bold');

			if ($pdf_font_regular != false) {
				$defaultConfig = (new ConfigVariables())->getDefaults();
				$fontDirs = $defaultConfig['fontDir'];

				$defaultFontConfig = (new FontVariables())->getDefaults();
				$fontData = $defaultFontConfig['fontdata'];

				$pdf = new Mpdf([
					'fontDir' => array_merge($fontDirs, [
						app_path('CoreFacturalo' . DIRECTORY_SEPARATOR . 'Templates' .
												DIRECTORY_SEPARATOR . 'pdf' .
												DIRECTORY_SEPARATOR . $base_template .
												DIRECTORY_SEPARATOR . 'font')
					]),
					'fontdata' => $fontData + [
						'custom_bold' => [
							'R' => $pdf_font_bold . '.ttf',
						],
						'custom_regular' => [
							'R' => $pdf_font_regular . '.ttf',
						],
					]
				]);
			}
		}

		$path_css = app_path('CoreFacturalo' . DIRECTORY_SEPARATOR . 'Templates' .
											 DIRECTORY_SEPARATOR . 'pdf' .
											 DIRECTORY_SEPARATOR . $base_template .
											 DIRECTORY_SEPARATOR . 'style.css');

		$stylesheet = file_get_contents($path_css);

		$pdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);
		$pdf->WriteHTML($html, HTMLParserMode::HTML_BODY);

		if (config('tenant.pdf_template_footer')) {
			$html_footer = $template->pdfFooter($base_template, $this->document);
			$pdf->SetHTMLFooter($html_footer);
		}

		$this->uploadFile($this->document->filename, $pdf->output('', 'S'), 'sale_note');
	}

	public function uploadFile($filename, $file_content, $file_type)
	{
		$this->uploadStorage($filename, $file_content, $file_type);
	}

	public function series()
	{
		return Series::where('establishment_id', auth()->user()->establishment_id)
					->where('document_type_id', '80')
					->get()
					->transform(function ($row) {
						return [
							'id'               => $row->id,
							'document_type_id' => $row->document_type_id,
							'number'           => $row->number
						];
					});
	}

	public function email(Request $request)
	{
		$company = Company::active();
		$record = SaleNote::find($request->input('id'));
		$customer_email = $request->input('email');

        Configuration::setConfigSmtpMail();
        Mail::to($customer_email)->send(new SaleNoteEmail($company, $record));

		return [
			'success' => true,
			'message' => 'Email enviado correctamente.'
		];
	}

	public function generateCPE(Request $request, $saleNoteId)
	{
        /**
         * codigo_tipo_documento => 01 = Factura || 03 = Factura
         * serie_documento
         * numero_documento
         * fecha_de_emision
         * hora_de_emision
         * fecha_de_vencimiento
         * codigo_condicion_de_pago
         **/
		$user = auth()->guard('api')->user();
		$saleNote = SaleNote::where('id', $saleNoteId)->first();
        if (!$saleNote) {
            return response()->json([
                'success' => false,
                'message' => 'La nota de venta asociada no existe.'
            ], 500);
        }
        $saleNote->items = $saleNote->items
            ->each(function($item) {
                $itemBD = Item::without(['item_type', 'unit_type', 'currency_type', 'warehouses','item_unit_types', 'tags'])
                    ->findOrFail($item->item_id);

                $itemToArray = json_decode(json_encode($item->item), true);
                $itemToArray['internal_id'] = $itemBD->internal_id ?? '';
                $itemToArray['item_code'] = $itemBD->item_code ?? '';
                $itemToArray['item_code_gs1'] = $itemBD->item_code_gs1 ?? '';
                $itemToArray['IdLoteSelected'] = null;

                $item->item = $itemToArray;
                return (object) $item;
            });
        $saleNote = $saleNote->toArray();

		$data = [
			'type'             => 'invoice',
			'group_id'         => '01',
			'user_id'          => $user->id,
			'external_id'      => $saleNote['external_id'],
			'establishment_id' => $saleNote['establishment_id'],
			'establishment'    => $saleNote['establishment'],
            "soap_type_id" => $saleNote['soap_type_id'],
            "state_type_id" => $saleNote['state_type_id'],
            "ubl_version" => "2.1",
            "filename" => "",
            "document_type_id" => $request->codigo_tipo_documento,
            "series" => $request->serie_documento,
            "number" => $request->numero_documento,
            "date_of_issue" => $request->fecha_de_emision,
            "time_of_issue" => $request->hora_de_emision,
            "customer_id" => $saleNote['customer_id'],
            "seller_id" => null,
            "customer" => $saleNote['customer'],
            "currency_type_id" => $saleNote['currency_type_id'],
            "purchase_order" => $saleNote['purchase_order'],
            "quotation_id" => $saleNote['quotation_id'],
            "order_note_id" => $saleNote['order_note_id'],
            "exchange_rate_sale" => $saleNote['exchange_rate_sale'],
            "total_prepayment" => $saleNote['total_prepayment'],
            "total_discount" => $saleNote['total_discount'],
            "total_charge" => $saleNote['total_charge'],
            "total_exportation" => $saleNote['total_exportation'],
            "total_free" => $saleNote['total_free'],
            "total_taxed" => $saleNote['total_taxed'],
            "total_unaffected" => $saleNote['total_unaffected'],
            "total_exonerated" => $saleNote['total_exonerated'],
            "total_igv" => $saleNote['total_igv'],
            "total_base_isc" => $saleNote['total_base_isc'],
            "total_isc" => $saleNote['total_isc'],
            "total_base_other_taxes" => $saleNote['total_base_other_taxes'],
            "total_other_taxes" => $saleNote['total_other_taxes'],
            "total_plastic_bag_taxes" => 0,
            "total_taxes" => $saleNote['total_taxes'],
            "total_value" => $saleNote['total_value'],
            "total" => $saleNote['total'],
            "has_prepayment" => 0,
            "affectation_type_prepayment" => null,
            "was_deducted_prepayment" => 0,
            "pending_amount_prepayment" => 0,
            "items" => $saleNote['items'],
            "charges" => $saleNote['charges'],
            "discounts" => $saleNote['discounts'],
            "prepayments" => $saleNote['prepayments'],
            "guides" => $saleNote['guides'],
            "related" => $saleNote['related'],
            "perception" => $saleNote['perception'],
            "detraction" => $saleNote['detraction'],
            "invoice" => [
                'operation_type_id' => "0101",
                'date_of_due' => $request->fecha_de_vencimiento,
            ],
            "hotel" => [],
            "transport" => [],
            "plate_number" => $saleNote['plate_number'],
            "legends" => [
                [ 'code' => 1000, 'value' => NumberLetter::convertToLetter($saleNote['total']) ]
            ],
            "actions" => [
                "send_email" => false,
                "send_xml_signed" => false,
                "format_pdf" => "a4",
            ],
            "payments" => [],
            "send_server" => 0,
            "payment_method_type_id" => $saleNote['payment_method_type_id'],
            "reference_data" => $saleNote['reference_data'],
            "fee" => [],
            'sale_note_id' => $saleNoteId,
            'payment_condition_id' => $request->codigo_condicion_de_pago,
		];



        $dataToRequest = new Request($data);

		$fact = DB::connection('tenant')->transaction(function () use ($dataToRequest) {
            $facturalo = new Facturalo();
            $facturalo->save($dataToRequest->all());
            $facturalo->createXmlUnsigned();
            $facturalo->signXmlUnsigned();
            $facturalo->updateHash();
            $facturalo->updateQr();
            $facturalo->createPdf();
            $facturalo->sendEmail();
            $facturalo->senderXmlSignedBill();

            return $facturalo;
        });

        $document = $fact->getDocument();
        $response = $fact->getResponse();

        return [
            'success' => true,
            'data' => [
                'number' => $document->number_full,
                'filename' => $document->filename,
                'external_id' => $document->external_id,
                'state_type_id' => $document->state_type_id,
                'state_type_description' => $this->getStateTypeDescription($document->state_type_id),
                'number_to_letter' => $document->number_to_letter,
                'hash' => $document->hash,
                'qr' => $document->qr,
            ],
            'links' => [
                'xml' => $document->download_external_xml,
                'pdf' => $document->download_external_pdf,
                'cdr' => ($response['sent']) ? $document->download_external_cdr : '',
            ],
            'response' => ($response['sent']) ? array_except($response, 'sent') : [],
        ];
	}

    private function getStateTypeDescription($id)
    {
        return StateType::find($id)->description;
    }
}
