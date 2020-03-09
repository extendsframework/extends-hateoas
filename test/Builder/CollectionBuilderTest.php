<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Builder;

use ExtendsFramework\Hateoas\ResourceInterface;
use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\Router\RouterInterface;
use PHPUnit\Framework\TestCase;

class CollectionBuilderTest extends TestCase
{
    /**
     * Build.
     *
     * Test that builder will return a resource with collection links, attributes and embedded resources.
     *
     * @covers \ExtendsFramework\Hateoas\Builder\CollectionBuilder::__construct()
     */
    public function testBuild(): void
    {
        $resource = $this->createMock(BuilderInterface::class);

        $request = $this->createMock(RequestInterface::class);

        $router = $this->createMock(RouterInterface::class);
        $router
            ->expects($this->exactly(3))
            ->method('assemble')
            ->withConsecutive(
                [
                    'route-name',
                    [
                        'foo' => 'bar',
                        'limit' => 20,
                        'page' => 2,
                    ]
                ],
                [
                    'route-name',
                    [
                        'foo' => 'bar',
                        'limit' => 20,
                        'page' => 1,
                    ]
                ],
                [
                    'route-name',
                    [
                        'foo' => 'bar',
                        'limit' => 20,
                        'page' => 3,
                    ]
                ]
            )
            ->willReturn($request);

        /**
         * @var RouterInterface $router
         * @var BuilderInterface $resource
         */
        $collection = new CollectionBuilder(
            $router,
            'route-name',
            [
                'foo' => 'bar',
            ],
            'rel',
            [
                $resource,
                $resource
            ],
            20,
            2,
            120
        );
        $built = $collection->build();

        $links = $built->getLinks();
        $this->assertSame($request, $links['self']->getRequest());
        $this->assertSame($request, $links['prev']->getRequest());
        $this->assertSame($request, $links['next']->getRequest());

        $attributes = $built->getAttributes();
        $this->assertSame(20, $attributes['limit']->getValue());
        $this->assertSame(2, $attributes['page']->getValue());
        $this->assertSame(120, $attributes['total']->getValue());

        $resources = $built->getResources();
        $this->assertCount(2, $resources['rel']);
        $this->assertContainsOnlyInstancesOf(ResourceInterface::class, $resources['rel']);
    }
}
