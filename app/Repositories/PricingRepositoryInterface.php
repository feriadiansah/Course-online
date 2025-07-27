<?php

namespace App\Repositories;

use App\Models\Pricing;
use Illuminate\support\Collection;

interface PricingRepositoryInterface
{
    public function findById(int $id): ?Pricing;

    public function getAll(): Collection;
}
