<?php

declare(strict_types=1);

namespace TinyContainer;

use Closure;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

/**
 * @template T of object
 */
class TinyContainer implements ContainerInterface
{
    /** @var Closure[] */
    private array $services;

    /**
     * @param Closure[] $services
     */
    public function __construct(array $services)
    {
        $this->services = $services;
    }

    /**
     * @param class-string<T> $id
     * @return T
     *
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     *
     */
    public function get($id)
    {
        $fn = $this->services[$id] ?? null;

        if (false === is_callable($fn)) {
            throw new NotFoundException($id);
        }

        try {
            $service = $fn($this);
        } catch (Throwable $e) {
            throw new ContainerException($id, $e);
        }

        return $service;
    }

    /**
     * @inheritDoc
     * @param class-string<T> $id
     * @return bool
     */
    public function has($id)
    {
        return isset($this->services[$id]);
    }
}
