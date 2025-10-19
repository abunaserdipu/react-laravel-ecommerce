<?php

namespace App\Services;

use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Auth;

class CartService
{

    private ?array $cachedCartItems = null;
    protected const COOKIE_NAME = 'cartItems';
    protected const COOKIE_LIFETIME = 60 * 24 * 365; // 1 year

    public function addItemToCart(Product $product, int $quantity = 1, $optionIds = null)
    {

    }

    public function updateItemQuantity(int $productId, int $quantity, $optionIds = null)
    {

    }

    public function removeItemFromCart(int $productId, $optionIds = null)
    {

    }

    public function getCartItems(): array
    {
        // We need to put this in try-catch, otherwise if something goes wrong,
        // the website will not open at all
        try {
            if ($this->cachedCartItems === null) {
                // If the user is authenticated, retrieve from the database
                if (Auth::check()) {
                    $cartItems = $this->getCartItemsFromDatabase();
                } else {
                    // If the user is a guest, retrive from cookies
                    $cartItems = $this->getCartItemsFromCookies();
                }

                $productIds = collect($cartItems)->map(fn($item) => $item['produt_id']);
                $products = Product::whereIn('id', $productIds)
                    ->with('user.vendor')
                    ->forWebsite()
                    ->get()
                    ->keyBy('id');

                $cartItemData = [];
                foreach ($cartItemData as $key => $cartItem) {
                    $product = data_get($products, $cartItem['product_id']);
                }
            }

            return $this->cachedCartItems;

        } catch (Exception $e) {

        }
    }

    public function getTotalQuantity(): int
    {

    }

    public function getTotalPrice(): float
    {

    }

    protected function updateQuantityItemInDatabase(int $productId, int $quantity, array $optionIds): void
    {

    }

    protected function updateItemQuantityInCookies(int $productId, int $quantity, array $optionIds): void
    {

    }

    protected function saveItemToDatabase(int $productId, int $quantity, $price)
    {

    }

    protected function saveItemToCookies(int $productId, int $quantity, $price)
    {

    }

    protected function removeItemFromDatabase(int $productId, array $optionIds)
    {

    }

    protected function removeItemFromCookies(int $productId, array $optionIds)
    {

    }

    protected function getCartItemsFromDatabase()
    {

    }

    protected function getCartItemsFromCookies()
    {

    }
}
