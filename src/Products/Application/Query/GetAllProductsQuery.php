<?php

declare(strict_types=1);


namespace App\Products\Application\Query;

interface GetAllProductsQuery
{
    public function execute(): ProductCollectionView;
}