<?php

declare(strict_types=1);

namespace TinyContainer\Tests\Unit;

use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use TinyContainer\ContainerException;
use TinyContainer\NotFoundException;
use TinyContainer\Tests\Fixtures\AnotherService;
use TinyContainer\Tests\Fixtures\MyService;
use TinyContainer\TinyContainer;
use function TinyContainer\resolve;

class TinyContainerTest extends TestCase
{
    /** @var TinyContainer<object> */
    private TinyContainer $container;

    protected function setUp(): void
    {
        $this->container = new TinyContainer([
            MyService::class => fn(ContainerInterface $container) => new MyService(
                new DateTimeImmutable('2020-01-01 15:15:15')
            ),
            AnotherService::class => fn(ContainerInterface $container) => new AnotherService(
                $container->get(MyService::class)
            ),
            'failing.service' => function (ContainerInterface $container): void {
                throw new Exception('something went wrong');
            },
            'non-callable.service' => new MyService(new DateTimeImmutable('2020-01-01 15:15:15')),
        ]);
    }

    public function testWhenServiceIsNotRegisteredThenThrowNotFoundException(): void
    {
        $id = 'non-registered.service';

        $this->expectException(NotFoundException::class);

        $this->container->get($id);
    }

    public function testWhenServiceIsNotCallableThenThrowContainerException(): void
    {
        $id = 'non-callable.service';

        $this->expectException(ContainerException::class);

        $this->container->get($id);
    }

    public function testWhenServiceFailsToCreateThenThrowException(): void
    {
        $id = 'failing.service';

        $this->expectExceptionObject(new Exception('something went wrong'));

        $this->container->get($id);
    }

    public function testWhenServiceIsRegisteredThenReturn(): void
    {
        $id = AnotherService::class;

        $service = $this->container->get($id);

        $this->assertEquals(new AnotherService(new MyService(new DateTimeImmutable('2020-01-01 15:15:15'))), $service);
    }

    public function testWhenServiceIsGotOnceThenCacheItsInstance(): void
    {
        $id = MyService::class;
        $service = $this->container->get($id);
        assert($service instanceof MyService);
        $service->replaceDateTime(new DateTimeImmutable('1997-12-12 19:19:19'));

        $cached = $this->container->get($id);
        assert($cached instanceof MyService);

        $this->assertEquals('1997-12-12 19:19:19', $cached->getDateTime()->format('Y-m-d H:i:s'));
    }

    public function testWhenServiceIsCreatedThenReturnNewInstanceWithoutChangingCachedInstance(): void
    {
        $id = MyService::class;
        $service = $this->container->get($id);
        assert($service instanceof MyService);
        $service->replaceDateTime(new DateTimeImmutable('1997-12-12 19:19:19'));

        $created = $this->container->create($id);
        assert($created instanceof MyService);
        $cached = $this->container->get($id);
        assert($cached instanceof MyService);

        $this->assertEquals('2020-01-01 15:15:15', $created->getDateTime()->format('Y-m-d H:i:s'));
        $this->assertEquals('1997-12-12 19:19:19', $cached->getDateTime()->format('Y-m-d H:i:s'));
    }
}
