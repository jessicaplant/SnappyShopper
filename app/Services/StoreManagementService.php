<?php

namespace App\Services;

use App\Models\Store;
use Illuminate\Database\Eloquent\Collection;

class StoreManagementService implements StoreManagementServiceInterface
{

    /**
     * @inheritDoc
     *
     * @param array $parameters
     * @return Store
     */
    public function createOne(array $parameters): Store
    {
        return Store::create($parameters)->fresh();
    }

    /**
     * @inheritDoc
     *
     * @param array $parameters
     * @return Collection
     */
    public function findMany(array $parameters): Collection
    {
        // TODO: Implement findMany() method.
    }
}
