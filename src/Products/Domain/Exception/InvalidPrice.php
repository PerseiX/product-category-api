<?php

declare(strict_types=1);

namespace App\Products\Domain\Exception;

use Exception;

final class InvalidPrice extends Exception
{
    public function __construct()
    {
        parent::__construct('The price is invalid');
    }
}
