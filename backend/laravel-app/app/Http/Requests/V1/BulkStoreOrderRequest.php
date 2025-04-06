<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkStoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        
        //no futuro criar 'order:create' caso a aplicação cresça
        return $user !== null && $user->tokenCan('create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            '*.customerId' => ['required', 'integer'],
            '*.price' => ['required', 'numeric'],
            '*.status' => ['required', Rule::in(['A','WA', 'D', 'a', 'wa', 'd'])],
            '*.creation_date' => ['required', 'date_format:Y-m-d H:i:s'],
            '*.paid_date' => ['date_format:Y-m-d H:i:s', 'nullable']
        ];
    }
    //handle camel case to db format
    protected function prepareForValidation()
    {
        $data = [];

        foreach ($this->toArray() as $obj) {
            $obj['customer_id'] = $obj['customerId'] ?? null;
            $obj['creation_date'] = $obj['creationDate'] ?? null;
            $obj['paid_date'] = $obj['paidDate'] ?? null;

            $data[] = $obj;
        }

        $this->merge($data);
    }
}
