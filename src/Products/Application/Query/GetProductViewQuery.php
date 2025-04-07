<?php

declare(strict_types=1);


namespace App\Products\Application\Query;

use Ramsey\Uuid\UuidInterface;

interface GetProductViewQuery
{
    public function execute(UuidInterface $uuid): ?ProductView;
}