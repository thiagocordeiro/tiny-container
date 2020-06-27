<?php

declare(strict_types=1);

namespace TinyContainer\Tests\Fixtures;

use DateTimeImmutable;

class MyService
{
    private DateTimeImmutable $dateTime;

    public function __construct(DateTimeImmutable $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public function getDateTime(): DateTimeImmutable
    {
        return $this->dateTime;
    }
}
