<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Serializer\Json;

use ExtendsFramework\Hateoas\Attribute\AttributeInterface;
use ExtendsFramework\Hateoas\Link\LinkInterface;
use ExtendsFramework\Hateoas\ResourceInterface;
use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\Http\Request\Uri\UriInterface;
use ExtendsFramework\Identity\IdentityInterface;
use ExtendsFramework\Router\RouterInterface;
use PHPUnit\Framework\TestCase;

class JsonSerializerTest extends TestCase
{
    /**
     * Test that serializer will serialize resource.
     *
     * @covers \ExtendsFramework\Hateoas\Serializer\Json\JsonSerializer::__construct()
     * @covers \ExtendsFramework\Hateoas\Serializer\Json\JsonSerializer::serialize()
     * @covers \ExtendsFramework\Hateoas\Serializer\Json\JsonSerializer::toArray()
     * @covers \ExtendsFramework\Hateoas\Serializer\Json\JsonSerializer::serializeLinks()
     * @covers \ExtendsFramework\Hateoas\Serializer\Json\JsonSerializer::serializeAttributes()
     * @covers \ExtendsFramework\Hateoas\Serializer\Json\JsonSerializer::serializeResources()
     */
    public function testSerialize(): void
    {
        $uri = $this->createMock(UriInterface::class);
        $uri
            ->expects($this->any())
            ->method('toRelative')
            ->willReturn('/api/blog/1');

        $request = $this->createMock(RequestInterface::class);
        $request
            ->expects($this->any())
            ->method('getUri')
            ->willReturn($uri);

        $router = $this->createMock(RouterInterface::class);
        $router
            ->expects($this->any())
            ->method('assemble')
            ->with('/api/blog', ['id' => 1])
            ->willReturn($request);

        $link = $this->createMock(LinkInterface::class);
        $link
            ->expects($this->any())
            ->method('getRequest')
            ->willReturn($request);

        $attribute = $this->createMock(AttributeInterface::class);
        $attribute
            ->expects($this->any())
            ->method('getValue')
            ->willReturnOnConsecutiveCalls(1, 2, 3, 4);

        $resource = $this->createMock(ResourceInterface::class);
        $resource
            ->expects($this->any())
            ->method('getLinks')
            ->willReturnOnConsecutiveCalls(
                ['self' => $link],
                ['self' => $link],
                ['self' => $link],
                ['self' => [$link, $link]]
            );

        $resource
            ->expects($this->any())
            ->method('getResources')
            ->willReturnOnConsecutiveCalls(['author' => [$resource, $resource, $resource]], [], [], []);

        $resource
            ->expects($this->any())
            ->method('getAttributes')
            ->willReturn(['id' => $attribute]);

        /**
         * @var RouterInterface $router
         * @var ResourceInterface $resource
         * @var IdentityInterface $identity
         */
        $serializer = new JsonSerializer($router);

        $this->assertSame(
            json_encode([
                '_links' => [
                    'self' => [
                        'href' => '/api/blog/1',
                    ],
                ],
                '_embedded' => [
                    'author' => [
                        [
                            '_links' => [
                                'self' => [
                                    'href' => '/api/blog/1',
                                ],
                            ],
                            'id' => 1,
                        ],
                        [
                            '_links' => [
                                'self' => [
                                    'href' => '/api/blog/1',
                                ],
                            ],
                            'id' => 2,
                        ],
                        [
                            '_links' => [
                                'self' => [
                                    [
                                        'href' => '/api/blog/1',
                                    ],
                                    [
                                        'href' => '/api/blog/1',
                                    ],
                                ],
                            ],
                            'id' => 3,
                        ]
                    ],
                ],
                'id' => 4,
            ]),
            $serializer->serialize($resource)
        );
    }

    /**
     * Test that empty _links and _embedded array will not be rendered where empty attributes must be rendered.
     *
     * @covers \ExtendsFramework\Hateoas\Serializer\Json\JsonSerializer::__construct()
     * @covers \ExtendsFramework\Hateoas\Serializer\Json\JsonSerializer::serialize()
     * @covers \ExtendsFramework\Hateoas\Serializer\Json\JsonSerializer::toArray()
     * @covers \ExtendsFramework\Hateoas\Serializer\Json\JsonSerializer::serializeLinks()
     * @covers \ExtendsFramework\Hateoas\Serializer\Json\JsonSerializer::serializeAttributes()
     * @covers \ExtendsFramework\Hateoas\Serializer\Json\JsonSerializer::serializeResources()
     */
    public function testEmptyProperties(): void
    {
        $router = $this->createMock(RouterInterface::class);

        $attribute = $this->createMock(AttributeInterface::class);
        $attribute
            ->expects($this->any())
            ->method('getValue')
            ->willReturnOnConsecutiveCalls(1, 'Title', null, '');

        $resource = $this->createMock(ResourceInterface::class);
        $resource
            ->expects($this->any())
            ->method('getAttributes')
            ->willReturn([
                'id' => $attribute,
                'title' => $attribute,
                'description' => $attribute,
                'comment' => $attribute,
            ]);

        /**
         * @var RouterInterface $router
         * @var ResourceInterface $resource
         * @var IdentityInterface $identity
         */
        $serializer = new JsonSerializer($router);
        $this->assertSame(
            json_encode([
                'id' => 1,
                'title' => 'Title',
                'description' => null,
                'comment' => '',
            ]),
            $serializer->serialize($resource)
        );
    }
}
