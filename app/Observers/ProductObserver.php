<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

final class ProductObserver
{
    public function created(Product $product): void
    {
        Cache::forget('products_all');
    }

    public function updated(Product $product): void
    {
        Cache::forget('products_all');
    }

    public function deleted(Product $product): void
    {
        Cache::forget('products_all');
    }
}
