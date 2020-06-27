# Tiny Container

Tiny container is a `psr/container` implementation which allows registering services to be instantiated only when needed, there is no fancy or complex features, for this I would recommend using [https://php-di.org/](https://php-di.org/).
The goal is to have container capabilities under psr interface for very tiny projects, when we don't even need a framework.

## Installing
```bash
composer require thiagocordeiro/tiny-container
```

## How to use

There is not much secret in using this tool.
```php
$config = [
    UserRepositoryInterface::class => fn(ContainerInterface $container) => new DoctrineUserRepository(),
    CacheInterface::class => fn(ContainerInterface $container) => new RedisCache(),
    'http.api-client' => fn(ContainerInterface $container) => new GuzzleClient([]),
    MyService::class => fn(ContainerInterface $container) => new MyService(
        $container->get(UserRepositoryInterface::class),
        $container->get(CacheInterface::class),
        $container->get('http.api-client'),
    ),
];

$container = \TinyContainer\TinyContainer($config);

$service = $container->get(MyService::class);
$service->doTheThing();
```

## Testing
```shell
composer run tests
```

## Contributing
Feel free to open issues and submit PRs
