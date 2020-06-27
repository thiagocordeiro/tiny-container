<?php

declare(strict_types=1);

namespace TinyContainer\Tests\Fixtures;

class AnotherService
{
    private MyService $myService;

    public function __construct(MyService $myService)
    {
        $this->myService = $myService;
    }

    public function getMyService(): MyService
    {
        return $this->myService;
    }
}
