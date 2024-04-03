<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /**
         * I would normally include checks here on top of a policy if needs be, but for now, let's
         * just let everyone in
         */
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'lat' => 'required',
            'long' => 'required',

            /**
             * Here is where I'd add some enum validation to say these values can only be one of X where X is
             * a value in the relevant enum. I might do it if I have time later.
             */
            'state' => 'required',
            'type' => 'required',
            'max_delivery_distance' => 'required',
        ];
    }
}
