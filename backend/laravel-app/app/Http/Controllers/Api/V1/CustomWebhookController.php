<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomWebhookController extends CashierWebhookController
{
    /**
     * Handle a Stripe webhook call.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleWebhook(Request $request)
    {
        $payload = json_decode($request->getContent(), true);
        $method = 'handle'.Str::studly(str_replace('.', '_', $payload['type']));

        info('Stripe Webhook Received:', $payload);

        if (method_exists($this, $method)) {
            $this->setMaxNetworkRetries();

            $response = $this->{$method}($payload);

            info('Stripe Webhook Handled:', $payload);

            return $response;
        }

        return $this->missingMethod($payload);
    }

    /**
     * Handle invoice payment succeeded.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleInvoicePaymentSucceeded(array $payload)
    {
        info("entrouNoInvoiceSucceded");
        $invoiceData = $payload['data']['object'];

        $user = User::where('email', $invoiceData['customer_email'])->first();
        $userId = $user ? $user->id : null;

        // Salve a fatura na base de dados
        Invoice::create([
            'stripe_id' => $invoiceData['id'],
            'stripe_customer_id' => $invoiceData['customer'],
            'user_id' => $userId,
            'amount_due' => $invoiceData['amount_due'],
            'amount_paid' => $invoiceData['amount_paid'],
            'amount_remaining' => $invoiceData['amount_remaining'],
            'currency' => $invoiceData['currency'],
            'status' => $invoiceData['status'],
            'invoice_pdf' => $invoiceData['invoice_pdf'],
            'hosted_invoice_url' => $invoiceData['hosted_invoice_url'],
            'created_at' => Carbon::createFromTimestamp($invoiceData['created']),
        ]);

        return $this->successMethod();
    }
}