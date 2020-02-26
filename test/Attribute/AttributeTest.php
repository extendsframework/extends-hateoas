<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Attribute;

use ExtendsFramework\Authorization\Permission\PermissionInterface;
use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{

    /**
     * Test that getter methods will return the correct values.
     *
     * @covers \ExtendsFramework\Hateoas\Attribute\Attribute::__construct()
     * @covers \ExtendsFramework\Hateoas\Attribute\Attribute::getValue()
     * @covers \ExtendsFramework\Hateoas\Attribute\Attribute::getPermission()
     */
    public function testGetters(): void
    {
        $permission = $this->createMock(PermissionInterface::class);

        /**
         * @var PermissionInterface $permission
         */
        $attribute = new Attribute(1, $permission);

        $this->assertSame(1, $attribute->getValue());
        $this->assertSame($permission, $attribute->getPermission());
    }
}
