<?php

namespace App\Enums;

enum StoreTypeEnum: string
{
    case RESTAURANT = 'restaurant';
    case TAKEAWAY = 'takeaway';
    case SHOP = 'shop';
    case UNKNOWN = 'unknown';
}
