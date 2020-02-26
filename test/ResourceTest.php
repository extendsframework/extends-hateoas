<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas;

use ExtendsFramework\Hateoas\Attribute\AttributeInterface;
use ExtendsFramework\Hateoas\Link\LinkInterface;
use PHPUnit\Framework\TestCase;

class ResourceTest extends TestCase
{
    /**
     * Test that getter methods will return the correct values.
     *
     * @covers \ExtendsFramework\Hateoas\Resource::__construct()
     * @covers \ExtendsFramework\Hateoas\Resource::getLinks()
     * @covers \ExtendsFramework\Hateoas\Resource::getAttributes()
     * @covers \ExtendsFramework\Hateoas\Resource::getResources()
     */
    public function testGetters(): void
    {
        $link = $this->createMock(LinkInterface::class);
        $attribute = $this->createMock(AttributeInterface::class);
        $embedded = $this->createMock(ResourceInterface::class);

        /**
         * @var LinkInterface $link
         * @var AttributeInterface $attribute
         * @var ResourceInterface $embedded
         */
        $resource = new Resource(
            [
                'self' => $link,
            ],
            [
                'id' => $attribute,
            ],
            [
                'author' => $embedded,
            ]
        );

        $this->assertSame([
            'self' => $link,
        ], $resource->getLinks());
        $this->assertSame([
            'id' => $attribute
        ], $resource->getAttributes());
        $this->assertSame([
            'author' => $embedded,
        ], $resource->getResources());
    }
}
