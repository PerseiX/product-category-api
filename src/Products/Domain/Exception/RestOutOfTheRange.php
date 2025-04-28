<?php

declare(strict_types=1);

namespace App\Products\Domain\Exception;

use Exception;

final class RestOutOfTheRange extends Exception
{
    public function __construct()
    {
        parent::__construct('Rest must be between 0 and 99');
    }
}
