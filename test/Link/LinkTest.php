<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Link;

use ExtendsFramework\Authorization\Permission\PermissionInterface;
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
     * @covers \ExtendsFramework\Hateoas\Link\Link::getPermission()
     */
    public function testGetters(): void
    {
        $request = $this->createMock(RequestInterface::class);

        $permission = $this->createMock(PermissionInterface::class);

        /**
         * @var RequestInterface $request
         * @var PermissionInterface $permission
         */
        $link = new Link($request, true, $permission);

        $this->assertTrue($link->isEmbeddable());
        $this->assertSame($request, $link->getRequest());
        $this->assertSame($permission, $link->getPermission());
    }
}
