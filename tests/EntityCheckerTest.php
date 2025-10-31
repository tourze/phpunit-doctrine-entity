<?php

declare(strict_types=1);

namespace Tourze\PHPUnitDoctrineEntity\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\EntityChecker;
use Tourze\PHPUnitDoctrineEntity\Tests\Fixtures\Entity\TestEntityWithBothAttributes;
use Tourze\PHPUnitDoctrineEntity\Tests\Fixtures\Entity\TestEntityWithEntityAttribute;
use Tourze\PHPUnitDoctrineEntity\Tests\Fixtures\Entity\TestEntityWithTableAttribute;
use Tourze\PHPUnitDoctrineEntity\Tests\Fixtures\TestEntityWithoutAttributes;

/**
 * @internal
 */
#[CoversClass(EntityChecker::class)]
final class EntityCheckerTest extends TestCase
{
    public function testIsEntityClassWithEntityAttribute(): void
    {
        $reflection = new \ReflectionClass(TestEntityWithEntityAttribute::class);
        $this->assertTrue(EntityChecker::isEntityClass($reflection));
    }

    public function testIsEntityClassWithTableAttribute(): void
    {
        $reflection = new \ReflectionClass(TestEntityWithTableAttribute::class);
        $this->assertTrue(EntityChecker::isEntityClass($reflection));
    }

    public function testIsEntityClassWithBothAttributes(): void
    {
        $reflection = new \ReflectionClass(TestEntityWithBothAttributes::class);
        $this->assertTrue(EntityChecker::isEntityClass($reflection));
    }

    public function testIsEntityClassWithoutAttributes(): void
    {
        $reflection = new \ReflectionClass(TestEntityWithoutAttributes::class);
        $this->assertFalse(EntityChecker::isEntityClass($reflection));
    }

    public function testIsEntityClassWithNonEntityClass(): void
    {
        $reflection = new \ReflectionClass(self::class);
        $this->assertFalse(EntityChecker::isEntityClass($reflection));
    }
}
