<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    // Retorna todas as invoices de um usuário
    public function getUserInvoices($userId, Request $request)
    {
        // Opcional: autorize se só admin ou o próprio user pode ver
        $user = User::findOrFail($userId);
        $this->authorize('view', $user);

        $invoices = Invoice::where('user_id', $userId)->get();

        return response()->json($invoices);
    }
}