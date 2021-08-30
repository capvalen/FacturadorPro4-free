<?php

namespace Modules\DocumentaryProcedure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return [
			'documentary_document_id' => 'required|numeric',
			'number'                  => 'required|numeric|min:1',
			'invoice'                 => 'required|string|max:15',
			'date_register'           => 'required|date',
			'time_register'           => 'required|date_format:G:i:s',
			'person_id'               => 'required|numeric|min:1',
			'subject'                 => 'required|max:250',
			'documentary_process_id'  => 'required|numeric|min:1',
			'person'                  => 'required',
		];
	}
}
