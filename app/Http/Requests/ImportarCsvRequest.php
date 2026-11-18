<?php

namespace App\Http\Requests;

class ImportarCsvRequest extends FormularioBase
{
    public function rules(): array
    {
        return ['archivo' => ['required', 'file', 'mimes:csv,txt', 'max:5120']];
    }
}
