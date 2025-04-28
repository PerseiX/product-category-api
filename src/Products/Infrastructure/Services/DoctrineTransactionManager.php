<?php

declare(strict_types=1);

namespace App\Products\Infrastructure\Services;

use App\Products\Application\Services\TransactionManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use LogicException;

final class DoctrineTransactionManager implements TransactionManager
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @throws Exception
     */
    public function execute(callable $callable): bool
    {
        $this->entityManager->beginTransaction();
        try {
            $result = $callable();

            if (!is_bool($result)) {
                throw new LogicException('Transactional callable must return a boolean.');
            }

            if ($result) {
                $this->entityManager->commit();

                return true;
            }

            $this->entityManager->rollback();

            return false;
        } catch (Exception $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }
}
