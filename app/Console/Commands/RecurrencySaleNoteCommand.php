<?php

namespace App\Console\Commands;

use App\Models\Tenant\Company;
use App\Models\Tenant\SaleNote;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class RecurrencySaleNoteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recurrency:sale-note';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recurrencia de notas de venta';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('The command was started');
        DB::connection('tenant')->transaction(function () {

            $today = Carbon::now()->format('Y-m-d');

            $sale_notes = SaleNote::where([['apply_concurrency', false], ['automatic_date_of_issue','<=', $today], ['enabled_concurrency', true]])->sharedLock()->get();

            foreach ($sale_notes as $sale_note) {
                // Log::info("init create");

                // $record = $this->createSaleNote($sale_note);

                // if($record['success']){

                //     $this->info("La nota de venta: {$record['record']->identifier} fue generada de forma automática");
                //     Log::info("La nota de venta: {$record['record']->identifier} fue generada de forma automática");

                // }else{
                //     $this->info("La nota de venta no fué generada de forma automática");
                // }
                $this->createSaleNote($sale_note);

            }
        });
        $this->info("The command is finished");

    }


    public function createSaleNote($sale_note)
    {
        $record = DB::connection('tenant')->transaction(function () use ($sale_note) {

            // dd($sale_note->establishment);
            // nota base
            $quantity_notes_replicate = 0;
            $init_type_period = $sale_note->type_period;
            $init_quantity_period = $sale_note->quantity_period;
            $today = new Carbon();
            $init_d_of_issue = new Carbon($sale_note->date_of_issue);

            if($init_type_period && $init_quantity_period > 0){

                $difference_m_y = ($init_type_period == 'month') ? $today->diffInMonths($init_d_of_issue): $today->diffInYears($init_d_of_issue);
                $quantity_notes_replicate = intval($difference_m_y / $init_quantity_period);
                // dd($quantity_notes_replicate);

                for ($i=1; $i <= $quantity_notes_replicate ; $i++) {

                    $sale_note = ($i === 1) ? $sale_note : $replicate_sale_note;

                    $sale_note->apply_concurrency = true;
                    $sale_note->update();

                    $replicate_sale_note = $sale_note->replicate();
                    $replicate_sale_note->external_id = Str::uuid()->toString();
                    $replicate_sale_note->state_type_id = '01';
                    $replicate_sale_note->date_of_issue = $sale_note->automatic_date_of_issue;
                    $replicate_sale_note->time_of_issue = date('H:i:s');
                    $replicate_sale_note->apply_concurrency = false;
                    $replicate_sale_note->total_canceled = false;
                    $replicate_sale_note->changed = false;

                    $type_period = $replicate_sale_note->type_period;
                    $quantity_period = $replicate_sale_note->quantity_period;
                    $d_of_issue = new Carbon($replicate_sale_note->date_of_issue);
                    $automatic_date_of_issue = null;

                    if($type_period && $quantity_period > 0){

                        $add_period_date = ($type_period == 'month') ? $d_of_issue->addMonths($quantity_period): $d_of_issue->addYears($quantity_period);
                        $automatic_date_of_issue = $add_period_date->format('Y-m-d');

                    }

                    $replicate_sale_note->automatic_date_of_issue = $automatic_date_of_issue;

                    $replicate_sale_note->save();

                    // dd($sale_note->items);

                    foreach($sale_note->items as $row){
                        // dd($row);

                        // $new->sale_note_id = $replicate_sale_note->id;
                        // $new->save();
                        $replicate_sale_note->items()->create(
                            [
                                'item_id' => $row->item_id,
                                'item' => $row->item,
                                'quantity' => $row->quantity,
                                'unit_value' => $row->unit_value,
                                'affectation_igv_type_id' => $row->affectation_igv_type_id,
                                'total_base_igv' => $row->total_base_igv,
                                'percentage_igv' => $row->percentage_igv,
                                'total_igv' => $row->total_igv,
                                'system_isc_type_id' => $row->system_isc_type_id,
                                'total_base_isc' => $row->total_base_isc,
                                'percentage_isc' => $row->percentage_isc,
                                'total_isc' => $row->total_isc,
                                'total_base_other_taxes' => $row->total_base_other_taxes,
                                'percentage_other_taxes' => $row->percentage_other_taxes,
                                'total_other_taxes' => $row->total_other_taxes,
                                'total_taxes' => $row->total_taxes,
                                'price_type_id' => $row->price_type_id,
                                'unit_price' => $row->unit_price,
                                'total_value' => $row->total_value,
                                'total_charge' => $row->total_charge,
                                'total_discount' => $row->total_discount,
                                'total' => $row->total,
                                'attributes' => $row->attributes,
                                'charges' => $row->charges,
                                'discounts' => $row->discounts,
                            ]
                        );

                    }

                    $name = [$replicate_sale_note->prefix,$replicate_sale_note->id,date('Ymd')];
                    $replicate_sale_note->filename = join('-', $name);
                    $replicate_sale_note->update();

                    $this->info("La nota de venta: {$replicate_sale_note->identifier} fue generada de forma automática");
                    Log::info("La nota de venta: {$replicate_sale_note->identifier} fue generada de forma automática");
                    // return $replicate_sale_note;

                }


            }

        });

        // return [
        //     'success' => true,
        //     'record' => $record
        // ];


    }
}
