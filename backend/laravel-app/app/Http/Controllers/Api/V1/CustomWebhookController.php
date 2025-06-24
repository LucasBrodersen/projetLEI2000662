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
    protected function handleCheckoutSessionCompleted(array $payload)
    {
        info("entrouNoCheckoutSessionCompleted");
    
        $session = $payload['data']['object'];
    
        if ($session['mode'] === 'subscription') {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    
            $user = User::where('stripe_id', $session['customer'])->first();
    
            if (!$user) {
                info("Usuário não encontrado para o checkout session");
                return $this->successMethod();
            }
    
            $stripeSubscriptionId = $session['subscription'];
    
            $stripeSubscription = \Stripe\Subscription::retrieve($stripeSubscriptionId);
            //preciso corrigir o nome e uma forma de informar se é mensal ou anual...
            $subscriptionName = $stripeSubscription->metadata->subscription_name ?? 'default';
            // Já foi criada pelo Cashier
            $subscription = $user->subscriptions()->where('stripe_id', $stripeSubscription->id)->first();
    
            if ($subscription) {
                // Atualiza só o nome
                $subscription->update([
                    'name' => $subscriptionName,
                ]);
    
                info("Subscription atualizada com nome correto!");
            } else {
                info("Subscription não encontrada no banco.");
            }
        } elseif ($session['mode'] === 'payment') {
            info("Single Payment completed.");
        
            $stripeCustomerId = $session['customer'];
            $user = User::where('stripe_id', $stripeCustomerId)->first();
        
            if (!$user) {
                info("Usuário não encontrado para pagamento único");
                return $this->successMethod();
            }
        
            // Record the payment into your Payments table
            \App\Models\Payment::create([
                'user_id' => $user->id,
                'amount' => $session['amount_total'] / 100, // Stripe uses cents, convert to real value
                'product_id' => $session['metadata']['product_id'] ?? null, // Optional: only if you passed metadata when creating session
                'payment_intent_id' => $session['payment_intent'],
                'payment_date' => \Carbon\Carbon::now(),
            ]);
        
            info("Pagamento único registrado na tabela de payments.");
        }
    
        return $this->successMethod();
    }
    
}