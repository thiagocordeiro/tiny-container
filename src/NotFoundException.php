<?php

declare(strict_types=1);

namespace TinyContainer;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends Exception implements NotFoundExceptionInterface
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Service %s was not registered', $id));
    }
}
