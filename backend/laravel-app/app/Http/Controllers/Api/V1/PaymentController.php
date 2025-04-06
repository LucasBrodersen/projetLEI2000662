<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Checkout\Session;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;

class PaymentController extends Controller
{
    // public function createCheckoutSession(Request $request)
    // {
    //     Stripe::setApiKey(env('STRIPE_SECRET'));

    //     $session = \Stripe\Checkout\Session::create([
    //         'payment_method_types' => ['card'],
    //         'line_items' => [
    //             [
    //                 'price_data' => [
    //                     'currency' => 'usd',
    //                     'product_data' => [
    //                         'name' => 'Plano de Assinatura Teste',
    //                     ],
    //                     'unit_amount' => 1000, // Preço em centavos ($10.00)
    //                 ],
    //                 'quantity' => 1,
    //             ],
    //         ],
    //         'mode' => 'payment',
    //         'success_url' => 'http://127.0.0.1:5173/welcome-page',
    //         'cancel_url' => 'http://127.0.0.1:5173/welcome-page',
    //         'metadata' => [
    //             'user_id' => auth()->id(), // Isso está OK, mas não é suficiente
    //         ],
    //         'payment_intent_data' => [
    //             'metadata' => [
    //                 'user_id' => auth()->id(), // Isso faz o metadata ser transferido para payment_intent
    //             ],
    //         ],
    //     ]);
        

    //     info('Stripe Session Created:', $session->toArray());


    //     $user = auth()->user();
        
    //     // Create or associate the Stripe customer if it doesn't exist
    //     info('Payment debug');
    //     info($user);
    //     info($user->hasStripeId());

    //     if (!$user->hasStripeId()) {
    //         $user->createAsStripeCustomer();
    //     }

    //     // Store session ID or subscription details if needed
    //     $user->update([
    //         'stripe_session_id' => $session->id, // You can store additional info if needed
    //     ]);

    //     return response()->json(['id' => $session->id]);
    // }


        public function createCheckoutSession(Request $request)
    {
        $user = auth()->user();

        if (!$user->hasStripeId()) {
            $user->createAsStripeCustomer(); // Cria cliente no Stripe se não existir
        }

        $checkoutSession = $user->checkout([
            [
                'price' => 'price_1Qve2R045jmwTFVdZo8WJy2i', // Substitua pelo ID do preço do Stripe
                'quantity' => 1,
            ],
        ], [
            'success_url' => 'http://127.0.0.1:5173/welcome-page',
            'cancel_url' => 'http://127.0.0.1:5173/welcome-page',
            'payment_method_types' => ['card', 'multibanco'], // Adicione os métodos de pagamento desejados
        ]);

        return response()->json(['id' => $checkoutSession->id]);
    }

    // public function handleStripeWebhook(Request $request)
    // {
    //     $payload = $request->all();
    //     info('Stripe Webhook Received:', $payload);
    
    //     if ($payload['type'] === 'payment_intent.succeeded') {
    //         $paymentIntent = $payload['data']['object'];
    //         info('Payment Intent Data:', $paymentIntent);
    
    //         // Obtém o ID do usuário do metadata
    //         $userId = $paymentIntent['metadata']['user_id'] ?? null;
    //         info('User ID from Metadata:', ['user_id' => $userId]);
    
    //         if ($userId) {
    //             $user = \App\Models\User::find($userId);
    //             if ($user) {
    //                 // Registrar o pagamento
    //                 Payment::create([
    //                     'customer_id' => (int)$user->id,
    //                     'amount' => $paymentIntent['amount'] / 100, // Convertendo para dólares
    //                     'payment_date' => now()

    //                 ]);
    
    //                 return response()->json(['message' => 'Payment recorded successfully']);
    //             }
    //         }
    //     }
    
    //     return response()->json(['message' => 'Webhook received']);
    // }

    public function subscribeToPlan(Request $request)
    {
        $user = auth()->user();
        Stripe::setApiKey(env('STRIPE_SECRET'));

    
        if (!$user->hasStripeId()) {
            $user->createAsStripeCustomer();
        }
    
        // Crie uma sessão de checkout
        $checkoutSession = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'customer' => $user->stripe_id,
            'line_items' => [[
                'price' => 'price_1Qve3G045jmwTFVdzXPUsCXh', // Substitua pelo ID do preço do Stripe
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => 'http://127.0.0.1:5173/welcome-page',
            'cancel_url' => 'http://127.0.0.1:5173/welcome-page',
        ]);
    
        return response()->json(['id' => $checkoutSession->id]);
    }

}
