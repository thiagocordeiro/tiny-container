<?php

declare(strict_types=1);

namespace TinyContainer;

use Closure;
use Psr\Container\ContainerInterface;
use Throwable;

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
     * @inheritDoc
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
     */
    public function has($id)
    {
        return isset($this->services[$id]);
    }
}
