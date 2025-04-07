<?php

declare(strict_types=1);

namespace App\Tests\kit;

use App\Products\Application\Services\TransactionManager;
use Exception;
use LogicException;

final class InMemoryTransactionManager implements TransactionManager
{
    public bool $forceFailure = false;
    public bool $forceException = false;

    /**
     * @param callable $callable
     *
     * @return bool
     *
     * @throws Exception
     */
    public function execute(callable $callable): bool
    {
        try {
            if ($this->forceException) {
                throw new Exception('Forced transaction exception');
            }

            $result = $callable();

            if (!is_bool($result)) {
                throw new LogicException('Transactional callable must return a boolean.');
            }

            return $this->forceFailure ? false : $result;
        } catch (Exception $e) {
            throw $e;
        }
    }
}