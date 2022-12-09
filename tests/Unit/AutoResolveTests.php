<?php

namespace TinyContainer\Tests\Unit;

use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use TinyContainer\Tests\Fixtures\AnotherService;
use TinyContainer\Tests\Fixtures\MyService;
use TinyContainer\TinyContainer;

class AutoResolveTests extends TestCase
{
    private const NOW = '2020-01-01 15:15:15';

    /** @var TinyContainer<object> */
    private TinyContainer $container;

    protected function setUp(): void
    {
        $this->container = new TinyContainer([
            DateTimeImmutable::class => fn(ContainerInterface $container) => new DateTimeImmutable(self::NOW),

            MyService::class => TinyContainer::resolve(MyService::class),
            AnotherService::class => TinyContainer::resolve(AnotherService::class),
            'non-callable.service' => new MyService(new DateTimeImmutable('2020-01-01 15:15:15')),
        ]);
    }

    function testGivenAClassFqnWhenServiceWasRegisteredWithResolverThenResolveItsInstances(): void
    {
        $id = AnotherService::class;

        $instance = $this->container->get($id);

        $this->assertInstanceOf($id, $instance);
    }


    function testGivenAStringThenThrowAnException(): void
    {
        $id = 'foo.bar';
        $resolver = TinyContainer::resolve('non-callable.service');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Class "non-callable.service" does not exist');

        $resolver($this->container);
    }
}
