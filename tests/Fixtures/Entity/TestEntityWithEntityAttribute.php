<?php

declare(strict_types=1);

namespace Tourze\PHPUnitDoctrineEntity\Tests\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class TestEntityWithEntityAttribute
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
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
