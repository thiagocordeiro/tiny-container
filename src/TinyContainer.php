<?php

declare(strict_types=1);

namespace TinyContainer;

use Psr\Container\ContainerInterface;

class TinyContainer implements ContainerInterface
{
    /** @var mixed[] */
    private array $services;

    /**
     * @param mixed[] $services
     */
    public function __construct(array $services)
    {
        $this->services = $services;
    }

    /**
     * @inheritDoc
     * @template T of object
     * @param class-string<T> $id
     * @return T
     */
    public function get($id)
    {
        $fn = $this->services[$id] ?? null;

        if (null === $fn) {
            throw new NotFoundException($id);
        }

        if (false === is_callable($fn)) {
            throw new ContainerException($id);
        }

        return $fn($this);
    }

    /**
     * @inheritDoc
     * @template T of object
     * @param class-string<T> $id
     * @return bool
     */
    public function has($id)
    {
        return isset($this->services[$id]);
    }
}
