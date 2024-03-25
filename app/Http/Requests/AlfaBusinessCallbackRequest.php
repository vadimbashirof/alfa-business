<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AlfaBusinessCallbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return false;
    }

    public function rules(): array
    {
        return [
            'transactions' => 'required|array',
            'transactions.*.object' => [
                'required',
                'string',
                Rule::in(['ul_transaction_default']),
            ],
            'transactions.*.organizationId' => 'required|string',
            'transactions.*.data' => 'required|array',
            'transactions.*.data.uuid' => 'required|string',
            'transactions.*.data.transactionId' => 'required|string',
            'transactions.*.data.amountRub' => 'required|array',
            'transactions.*.data.amountRub.amount' => 'required|numeric',
            'transactions.*.data.correspondingAccount' => 'required|numeric',
            'transactions.*.data.direction' => [
                'required',
                Rule::in(['DEBIT', 'CREDIT']),
            ],
            'transactions.*.data.documentDate' => 'required|string',
            'transactions.*.data.number' => 'required|numeric',
            'transactions.*.data.operationCode' => 'required|numeric',
            'transactions.*.data.operationDate' => 'required|string',
            'transactions.*.data.paymentPurpose' => 'required|string',
            'transactions.*.data.rurTransfer' => 'required|array',
            'transactions.*.data.rurTransfer.payerInn' => 'required|numeric',
            'transactions.*.data.rurTransfer.payerKpp' => 'nullable|numeric',
            'transactions.*.data.rurTransfer.payeeAccount' => 'required|numeric',
            'transactions.*.data.rurTransfer.payerAccount' => 'required|numeric',
        ];
    }

    protected function prepareForValidation()
    {
        $this->replace([
            'transactions' => $this->all(),
        ]);
    }
}
