<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Expander;

use ExtendsFramework\Hateoas\Builder\BuilderInterface;
use ExtendsFramework\Hateoas\Link\LinkInterface;
use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\Http\Response\ResponseInterface;
use ExtendsFramework\Router\Controller\Executor\ExecutorInterface;
use ExtendsFramework\Router\Route\RouteMatchInterface;
use ExtendsFramework\Router\RouterInterface;
use PHPUnit\Framework\TestCase;

class ExpanderTest extends TestCase
{
    /**
     * Expand.
     *
     * Test that link will be expanded will builder returned from controller.
     *
     * @covers \ExtendsFramework\Hateoas\Expander\Expander::__construct()
     * @covers \ExtendsFramework\Hateoas\Expander\Expander::expand()
     */
    public function testExpand(): void
    {
        $builder = $this->createMock(BuilderInterface::class);

        $request = $this->createMock(RequestInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($builder);

        $match = $this->createMock(RouteMatchInterface::class);

        $router = $this->createMock(RouterInterface::class);
        $router
            ->expects($this->once())
            ->method('route')
            ->with($request)
            ->willReturn($match);

        $executor = $this->createMock(ExecutorInterface::class);
        $executor
            ->expects($this->once())
            ->method('execute')
            ->with($request, $match)
            ->willReturn($response);

        $link = $this->createMock(LinkInterface::class);
        $link
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        /**
         * @var RouterInterface $router
         * @var ExecutorInterface $executor
         * @var LinkInterface $link
         */
        $expander = new Expander($router, $executor);

        $this->assertSame($builder, $expander->expand($link));
    }
}
