<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Link;

use ExtendsFramework\Authorization\Permission\PermissionInterface;
use ExtendsFramework\Authorization\Policy\PolicyInterface;
use ExtendsFramework\Authorization\Role\RoleInterface;
use ExtendsFramework\Http\Request\RequestInterface;
use PHPUnit\Framework\TestCase;

class LinkTest extends TestCase
{
    /**
     * Test that getter methods will return the correct values.
     *
     * @covers \ExtendsFramework\Hateoas\Link\Link::__construct()
     * @covers \ExtendsFramework\Hateoas\Link\Link::getRequest()
     * @covers \ExtendsFramework\Hateoas\Link\Link::isEmbeddable()
     * @covers \ExtendsFramework\Hateoas\Link\Link::getRole()
     * @covers \ExtendsFramework\Hateoas\Link\Link::getPermission()
     * @covers \ExtendsFramework\Hateoas\Link\Link::getPolicy()
     */
    public function testGetters(): void
    {
        $request = $this->createMock(RequestInterface::class);

        $role = $this->createMock(RoleInterface::class);

        $permission = $this->createMock(PermissionInterface::class);

        $policy = $this->createMock(PolicyInterface::class);

        /**
         * @var RequestInterface $request
         * @var RoleInterface $role
         * @var PermissionInterface $permission
         * @var PolicyInterface $policy
         */
        $link = new Link($request, true, $role, $permission, $policy);

        $this->assertTrue($link->isEmbeddable());
        $this->assertSame($request, $link->getRequest());
        $this->assertSame($role, $link->getRole());
        $this->assertSame($permission, $link->getPermission());
        $this->assertSame($policy, $link->getPolicy());
    }
}
