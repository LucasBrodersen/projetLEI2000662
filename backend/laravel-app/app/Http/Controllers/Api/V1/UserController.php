<?php

namespace App\Http\Controllers\Api\V1;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Filters\V1\UserFilter;
use App\Http\Resources\V1\UserCollection;
use App\Http\Resources\V1\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Rules\StrongPassword;
use Illuminate\Support\Arr;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Webhook;

class UserController extends Controller
{
    public function listUsers(Request $request)
    {
        // Authorize based on role (only admins)
        if ($request->user()->type !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        // if ($request->user()->type !== 'admin' || $request->user()->type !== 'partner') {
        //     return response()->json(['error' => 'Unauthorized'], 403);
        // }
        $this->authorize('viewAny', User::class); // Check viewAny permission

        $filter = new UserFilter();
        $filterItems = $filter->transform($request); //['column', 'operator', 'value']
        $includeOrders = $request->query('includeOrders');
        $includeSubscription = $request->query('includeSubscription');
        $planNameFilter = $request->query('planName'); // Get the planName filter from the request

        $users = User::where($filterItems);

    // Filter by planName if provided
    if ($planNameFilter) {
        // If planName is an array (like planName[like]=value)
        $planNameValue = Arr::get($planNameFilter, 'like', $planNameFilter); // Get the 'like' value or use the whole value if not an array
        
        $users = $users->whereHas('activeSubscription', function ($query) use ($planNameValue) {
            $query->where('name', 'like', "%$planNameValue%"); // Filter subscriptions by plan name
        });
    }

        //TODO: ADD to many with orders
        if ($includeOrders) {
            $users = $users->with('orders');
        }

        // Se o parâmetro includeSubscription for passado, inclui a assinatura ativa
        if ($includeSubscription) {
            $users = $users->with('activeSubscription');
        }
        return new UserCollection($users->paginate()->appends($request->query())); 

    }

    public function showUser($id, Request $request)
    {
        // Ensure only admins can view a specific user
        $user = User::findOrFail($id); // Encontre o usuário pelo ID
        info('preAuthorize');
        $this->authorize('view', $user); // Autorização do usuário
        info('afterAuthorize');
        $includeSubscription = $request->query('includeSubscription'); // Verifica se o parâmetro de subscription foi passado
    
        if ($includeSubscription) {
            // Use load para carregar a relação de subscription para o usuário
            $user->load('activeSubscription');
        }
    
        // If user not found, return a 404 response
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        // Return the user as a resource
        return new UserResource($user);  // Retorna o recurso do usuário
    }

    // Method to update a user
    public function updateUser(Request $request, $id)
    {
        // if ($request->user()->type !== 'admin') {
        //     return response()->json(['error' => 'Unauthorized'], 403);
        // }
        // Find the user by ID
        $user = User::findOrFail($id);
        $this->authorize('update', $user);

        if ($request->has('type')) {
            $this->authorize('updateType', $user);
            $user->type = $request->input('type');
        }

        if ($request->has('enrolmentDate')) {
            $this->authorize('updateEnrolmentDate', $user);
            $user->enrolmentDate = $request->input('enrolmentDate');
        }

        // the below might be unecessary as user will not be authorized
        // Se não for admin, remova enrolmentDate e type do request para evitar alteração
        if ($request->user()->type !== 'admin') {
            $request->request->remove('enrolmentDate');
            $request->request->remove('type');
        }
        
        // If 'passwordConfirmation' is provided, rename it to 'password_confirmation'
         if ($request->has('passwordConfirmation')) {
            $request->merge(['password_confirmation' => $request->input('passwordConfirmation')]);
        }


        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email,' . $id, // Ignore current user's email for unique check
            'password' => ['nullable', 'string', 'confirmed', new StrongPassword],
            'type' => 'required|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'postalCode' => 'nullable|string',
            'enrolmentDate' => 'nullable|date',
            'status' => 'nullable|in:active,inactive,suspended,pending', // Validate against the possible enum values
        ]);

        // Update user information
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->type = $validatedData['type'];
        $user->address = $validatedData['address'] ?? null;
        $user->city = $validatedData['city'] ?? null;
        $user->state = $validatedData['state'] ?? null;
        $user->postal_code = $validatedData['postalCode'] ?? null;
        $user->enrolment_date = $validatedData['enrolmentDate'] ?? null;
        $user->status = $validatedData['status'];

        // Check if password is provided and update it
        if ($request->filled('password')) {
            $user->password = Hash::make($validatedData['password']);
        }

        // Save the updated user data
        $user->save();

        // Return success response
        return response()->json(['message' => 'User updated successfully'], 200);
    }

    public function deleteUser($id, Request $request)
    {
        // Authorize based on role (only admins)
        if ($request->user()->type !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        // Find the user by ID
        $user = User::findOrFail($id);
        $this->authorize('delete', $user); 

        // Check if user exists
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        // Delete the user
        $user->delete();
    
        // Return a success response
        return response()->json(['message' => 'User deleted successfully'], 200);
    }

    // public function createCheckoutSession(Request $request)
    // {
    //     Stripe::setApiKey(env('STRIPE_SECRET'));

    //     // Create a Stripe Checkout session
    //     $session = Session::create([
    //         'payment_method_types' => ['card'],
    //         'line_items' => [
    //             [
    //                 'price_data' => [
    //                     'currency' => 'usd',
    //                     'product_data' => [
    //                         'name' => 'Plano de Assinatura Teste',
    //                     ],
    //                     'unit_amount' => 1000,  // Amount in cents (1000 = $10.00 USD)
    //                 ],
    //                 'quantity' => 1,
    //             ],
    //         ],
    //         'mode' => 'payment',
    //         'success_url' => route('payment.success'),
    //         'cancel_url' => route('payment.cancel'),
    //     ]);

    //     // Ensure user is authenticated and create as Stripe customer if not already done
    //     $user = $request->user();
        
    //     if (!$user->hasStripeId()) {
    //         $user->createAsStripeCustomer();
    //     }

    //     // Optionally store the session ID for future reference
    //     $user->update([
    //         'stripe_session_id' => $session->id,
    //     ]);

    //     return response()->json(['url' => $session->url]);
    // }

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');
        $event = null;

        try {
            // Verify the webhook signature
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Webhook verification failed.'], 400);
        }

        // Handle the 'checkout.session.completed' event
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            // Get the Stripe customer ID
            $stripeCustomerId = $session->customer;

            // Find the user by Stripe customer ID (you should already have this in your database)
            $user = User::where('stripe_id', $stripeCustomerId)->first();

            if ($user) {
                // Update user with subscription or payment details
                $user->update([
                    'stripe_subscription_id' => $session->subscription ?? null,
                ]);

                // Handle further actions like sending a confirmation email, etc.
            }
        }

        return response()->json(['message' => 'Webhook received successfully'], 200);
    }
}
