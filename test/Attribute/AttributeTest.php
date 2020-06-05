<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Attribute;

use ExtendsFramework\Authorization\Permission\PermissionInterface;
use ExtendsFramework\Authorization\Policy\PolicyInterface;
use ExtendsFramework\Authorization\Role\RoleInterface;
use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{

    /**
     * Test that getter methods will return the correct values.
     *
     * @covers \ExtendsFramework\Hateoas\Attribute\Attribute::__construct()
     * @covers \ExtendsFramework\Hateoas\Attribute\Attribute::getValue()
     * @covers \ExtendsFramework\Hateoas\Attribute\Attribute::getRole()
     * @covers \ExtendsFramework\Hateoas\Attribute\Attribute::getPermission()
     * @covers \ExtendsFramework\Hateoas\Attribute\Attribute::getPolicy()
     */
    public function testGetters(): void
    {
        $role = $this->createMock(RoleInterface::class);

        $permission = $this->createMock(PermissionInterface::class);

        $policy = $this->createMock(PolicyInterface::class);

        /**
         * @var RoleInterface $role
         * @var PermissionInterface $permission
         * @var PolicyInterface $policy
         */
        $attribute = new Attribute(1, $role, $permission, $policy);

        $this->assertSame(1, $attribute->getValue());
        $this->assertSame($role, $attribute->getRole());
        $this->assertSame($permission, $attribute->getPermission());
        $this->assertSame($policy, $attribute->getPolicy());
    }
}
