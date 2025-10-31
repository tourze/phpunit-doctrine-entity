<?php

declare(strict_types=1);

namespace Tourze\PHPUnitDoctrineEntity;

use Doctrine\ORM\Mapping as ORM;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitBase\TestCaseHelper;

/**
 * 实体测试的抽象基类.
 *
 * 自动测试所有公共属性的 getter 和 setter 方法.
 * 子类需要实现抽象方法:
 * 1. createEntity(): 创建被测实体的一个新实例.
 */
abstract class AbstractEntityTestCase extends TestCase
{
    /**
     * 这个场景，没必要使用 RunTestsInSeparateProcesses 注解的
     */
    #[Test]
    final public function testShouldNotHaveRunTestsInSeparateProcesses(): void
    {
        $reflection = new \ReflectionClass($this);
        $this->assertEmpty(
            $reflection->getAttributes(RunTestsInSeparateProcesses::class),
            get_class($this) . '这个测试用例，不应使用 RunTestsInSeparateProcesses 注解'
        );
    }

    #[Test]
    final public function testIsEntityClass(): void
    {
        $coverClass = TestCaseHelper::extractCoverClass(new \ReflectionClass($this));
        $this->assertNotNull($coverClass, '测试用例 ' . get_class($this) . '必须使用 CoversClass 声明测试的目标');
        $this->assertIsString($coverClass);
        /** @var class-string $coverClassName */
        $coverClassName = $coverClass;
        $this->assertTrue(
            EntityChecker::isEntityClass(new \ReflectionClass($coverClassName)),
            '测试用例 ' . get_class($this) . ' 的测试目标必须是一个实体，请检查测试用例的实现'
        );
    }

    #[Test]
    final public function testDisallowUseMultipleCoversClass(): void
    {
        $this->assertCount(
            1,
            (new \ReflectionClass($this))->getAttributes(CoversClass::class),
            '单个测试用例必须只测试一个类，所以请删除 ' . get_class($this) . '中 重复的 CoversClass'
        );
    }

    /**
     * @param mixed $sampleValue
     */
    #[DataProvider('propertiesProvider')]
    final public function testGettersAndSetters(string $property, $sampleValue): void
    {
        $entity = $this->createEntity();

        $setter = 'set' . ucfirst($property);
        $getter = 'get' . ucfirst($property);

        // 某些属性可能是 bool 类型，getter 可能是 is/has 开头
        if (is_bool($sampleValue)) {
            if (method_exists($entity, 'is' . ucfirst($property))) {
                $getter = 'is' . ucfirst($property);
            } elseif (method_exists($entity, 'has' . ucfirst($property))) {
                $getter = 'has' . ucfirst($property);
            }
        }

        $this->assertTrue(
            method_exists($entity, $setter),
            sprintf('Setter method "%s" does not exist in class "%s".', $setter, get_class($entity))
        );

        $this->assertTrue(
            method_exists($entity, $getter),
            sprintf('Getter method "%s" does not exist in class "%s".', $getter, get_class($entity))
        );

        // 调用 setter
        $entity->{$setter}($sampleValue);

        // 调用 getter 并断言
        $this->assertSame($sampleValue, $entity->{$getter}());
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     * 子类需要重写此方法提供实际的属性和样本值.
     */
    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        // 默认返回空，子类需要重写此方法
        return [];
    }

    #[Test]
    final public function preferUseIndexColumnAttributeToDefineIndexWhenOnlyOneColumn(): void
    {
        $cover = TestCaseHelper::extractCoverClass(new \ReflectionClass($this));
        $this->assertNotNull($cover);
        $this->assertIsString($cover);
        /** @var class-string $coverClassName */
        $coverClassName = $cover;
        $coverClass = new \ReflectionClass($coverClassName);
        $this->assertTrue(EntityChecker::isEntityClass($coverClass), 'CoversClass指定的类，必须是一个实体类');

        foreach ($coverClass->getAttributes(ORM\Index::class) as $attribute) {
            $index = $attribute->newInstance();
            $this->assertInstanceOf(ORM\Index::class, $index);
            $columns = $index->columns ?? [];
            $this->assertGreaterThan(
                1,
                count($columns),
                "删除 {$index->name} 这个ORM\\Index注解，改用 IndexColumn 注解，直接在对应属性上声明索引。use Tourze\\DoctrineIndexedBundle\\Attribute\\IndexColumn; 直接在属性上使用 #[IndexColumn]",
            );
        }
    }

    /**
     * 创建被测实体的一个实例.
     *
     * @return object the entity instance to be tested
     */
    abstract protected function createEntity(): object;
}
