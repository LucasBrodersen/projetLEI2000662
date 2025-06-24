<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;

class StripeProductService
{
    /**
     * Buscar todos os produtos e seus preços do Stripe.
     *
     * @return array
     */
    public function getProductsAndPrices()
    {
        // Defina sua chave secreta do Stripe
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Recuperar todos os produtos
        $products = Product::all();

        // Preparar array para armazenar produtos e seus preços
        $productsWithPrices = [];

        foreach ($products as $product) {
            // Para cada produto, vamos pegar os preços associados a ele
            $prices = Price::all([
                'product' => $product->id // Filtra os preços pelo ID do produto
            ]);

            // Adiciona os preços aos produtos
            $productsWithPrices[] = [
                'product' => $product,
                'prices' => $prices,
            ];
        }

        return $productsWithPrices;
    }
}
