<?php

declare(strict_types=1);

namespace Tourze\PHPUnitDoctrineEntity\Tests\Fixtures;

class TestEntityWithoutAttributes
{
    private int $id;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
