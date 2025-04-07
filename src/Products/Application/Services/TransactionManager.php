<?php

declare(strict_types=1);


namespace App\Products\Application\Services;

interface TransactionManager
{
    public function execute(callable $callable): bool;
}
