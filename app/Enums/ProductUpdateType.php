<?php

namespace App\Enums;

enum ProductUpdateType: string
{
    case PRICE = 'price';
    case STOCK = 'stock';
    case DESCRIPTION = 'description';
    case IMAGES = 'images';
    case TAGS = 'tags';
}
