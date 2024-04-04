<?php

namespace App\Services;

use App\Models\Store;
use Illuminate\Support\Collection;

interface StoreManagementServiceInterface
{
    /**
     * Create a single Store
     *
     * @param array $parameters
     * @return Store
     */
    public function createOne(array $parameters): Store;

    /**
     * Return a collection of Stores matching passed parameters
     *
     * @param array $parameters
     * @return Collection
     */
    public function findMany(array $parameters): Collection;
}
