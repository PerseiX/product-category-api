<?php

declare(strict_types=1);

namespace App\Products\Domain\Exception;

use Exception;

final class CodeIsTooLong extends Exception
{
    public function __construct()
    {
        parent::__construct('Code must be at most 10 characters long');
    }
}
