<?php
namespace App\Http\Controllers\Api\V1;

use Stripe\Stripe;
use Stripe\Webhook;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        info("Chamou na webhook");
        // Verify the webhook signature
        $endpointSecret = env('STRIPE_SECRET');
        $event = null;

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\Exception $e) {
            return response('Webhook verification failed.', 400);
        }

        // Handle the successful payment
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            // Retrieve the Stripe customer
            $stripeCustomerId = $session->customer;

            // Find or create the user from the database
            $user = User::where('stripe_id', $stripeCustomerId)->first();

            if ($user) {
                // The customer is already linked; you can update user info here if needed
                // For example, add a subscription
                $user->update([
                    'stripe_subscription_id' => $session->subscription,
                ]);
            } else {
                // Create a new user or link customer to existing user
            }
        }

        return response('Webhook received successfully', 200);
    }
}

