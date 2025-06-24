<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;

use App\Services\StripeProductService;

class ProductController extends Controller
{
    protected $stripeProductService;

    public function __construct(StripeProductService $stripeProductService)
    {
        $this->stripeProductService = $stripeProductService;
    }

    public function index()
    {
        // Busca os produtos e preços do Stripe
        $productsWithPrices = $this->stripeProductService->getProductsAndPrices();

        // Passa os dados para a view ou retorna como JSON
        return response()->json($productsWithPrices);
    }
}
