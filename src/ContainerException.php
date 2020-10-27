<?php

declare(strict_types=1);

namespace TinyContainer;

use Exception;
use Psr\Container\ContainerExceptionInterface;

class ContainerException extends Exception implements ContainerExceptionInterface
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Unable to retrieve service %s from container', $id), 0);
    }
}
