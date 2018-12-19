<?php

namespace App\Http\Requests;

use App\Http\Models\Order;

class OrderStoreRequest extends AbstractFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'origin' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    if (count($value) !== 2 || empty($value[0]) || empty($value[1])) {
                        $fail($attribute.' is invalid.');
                    }
                },
            ],
            'destination' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    if (count($value) !== 2 || empty($value[0]) || empty($value[1])) {
                        $fail($attribute.' is invalid.');
                    }
                },
            ],
        ];
    }

        /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'origin.required' => 'Missing Origin Parameter',
            'page.array' => 'Invalid Origin Type',
            'destination.required' => 'Missing Destination Parameter',
            'destination.array' => 'Invalid Destination Type',
        ];
    }
}
