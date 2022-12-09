<?php

declare(strict_types=1);

namespace TinyContainer;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionParameter;

/**
 * @template T of object
 */
class TinyContainer implements ContainerInterface
{
    /** @var mixed[] */
    private array $services;

    /** @var array<string, T|object> */
    private array $instances;

    /**
     * @param mixed[] $services
     */
    public function __construct(array $services)
    {
        $this->services = $services;
    }

    /**
     * @inheritDoc
     * @param class-string<T>|string $id
     * @return T|object
     */
    public function get($id)
    {
        $this->instances[$id] ??= $this->create($id);

        return $this->instances[$id];
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     * @param class-string<T>|string $id
     * @return T|object
     * @throws NotFoundException
     * @throws ContainerException
     */
    public function create(string $id)
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
     * @param class-string<T>|string $id
     * @return bool
     */
    public function has($id)
    {
        return isset($this->services[$id]);
    }

    public static function resolve(string $class)
    {
        return function (ContainerInterface $container) use ($class): object {
            $reflection = new ReflectionClass($class);
            $constructor = $reflection->getConstructor();

            $params = array_map(
                fn(ReflectionParameter $parameter) => $container->get($parameter->getType()->getName()),
                $constructor->getParameters()
            );

            return new $class(...$params);
        };
    }
}
