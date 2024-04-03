<?php

namespace App\Http\Resources;

use App\Models\Store;
use Illuminate\Http\Resources\Json\ResourceCollection;

class StoreResourceCollection extends ResourceCollection
{
    public $collects = Store::class;
}
