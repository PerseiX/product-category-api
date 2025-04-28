<?php

declare(strict_types=1);

namespace App\Products\Domain\Exception;

use Exception;

final class WholeNegative extends Exception
{
    public function __construct()
    {
        parent::__construct('Whole part cannot be negative');
    }
}
