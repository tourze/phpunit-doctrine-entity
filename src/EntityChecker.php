<?php

declare(strict_types=1);

namespace Tourze\PHPUnitDoctrineEntity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

class EntityChecker
{
    /**
     * @param \ReflectionClass<object> $nativeReflection
     */
    public static function isEntityClass(\ReflectionClass $nativeReflection): bool
    {
        // 检查 Doctrine\ORM\Mapping\Entity 属性
        $entityAttributes = $nativeReflection->getAttributes(Entity::class);
        if (count($entityAttributes) > 0) {
            return true;
        }

        // 检查 Doctrine\ORM\Mapping\Table 属性
        $tableAttributes = $nativeReflection->getAttributes(Table::class);
        if (count($tableAttributes) > 0) {
            return true;
        }

        return false;
    }
}
