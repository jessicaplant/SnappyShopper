<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStoreRequest;
use App\Http\Requests\FindManyStoresRequest;
use App\Http\Resources\StoreResource;
use App\Http\Resources\StoreResourceCollection;
use App\Services\StoreManagementServiceInterface;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function create(CreateStoreRequest $request, StoreManagementServiceInterface $storeManagementService): StoreResource
    {
        $store = $storeManagementService->createOne($request->validated());

        return new StoreResource($store);
    }

    public function findMany(FindManyStoresRequest $request, StoreManagementServiceInterface $storeManagementService): StoreResourceCollection
    {
        $stores = $storeManagementService->findMany($request->validated());

        return new StoreResourceCollection($stores);
    }
}
