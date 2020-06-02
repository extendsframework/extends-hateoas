<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Framework\Http\Middleware\Hateoas;

use ExtendsFramework\Authorization\AuthorizerInterface;
use ExtendsFramework\Hateoas\Builder\BuilderInterface;
use ExtendsFramework\Hateoas\Builder\Exception\AttributeNotFound;
use ExtendsFramework\Hateoas\Builder\Exception\LinkNotEmbeddable;
use ExtendsFramework\Hateoas\Builder\Exception\LinkNotFound;
use ExtendsFramework\Hateoas\Expander\ExpanderInterface;
use ExtendsFramework\Hateoas\Framework\ProblemDetails\AttributeNotFoundProblemDetails;
use ExtendsFramework\Hateoas\Framework\ProblemDetails\LinkNotEmbeddableProblemDetails;
use ExtendsFramework\Hateoas\Framework\ProblemDetails\LinkNotFoundProblemDetails;
use ExtendsFramework\Hateoas\ResourceInterface;
use ExtendsFramework\Hateoas\Serializer\SerializerInterface;
use ExtendsFramework\Http\Middleware\Chain\MiddlewareChainInterface;
use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\Http\Request\Uri\UriInterface;
use ExtendsFramework\Http\Response\ResponseInterface;
use ExtendsFramework\Identity\IdentityInterface;
use ExtendsFramework\Security\SecurityServiceInterface;
use PHPUnit\Framework\TestCase;

class HateoasMiddlewareTest extends TestCase
{
    /**
     * Test that middleware gets response from middleware chain and serializes resource from body.
     *
     * @covers \ExtendsFramework\Hateoas\Framework\Http\Middleware\Hateoas\HateoasMiddleware::__construct()
     * @covers \ExtendsFramework\Hateoas\Framework\Http\Middleware\Hateoas\HateoasMiddleware::process()
     */
    public function testProcess(): void
    {
        $uri = $this->createMock(UriInterface::class);
        $uri
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn([
                'foo' => 'bar',
                'expand' => [
                    'first',
                ],
                'project' => [
                    'second',
                ],
            ]);

        $uri
            ->expects($this->once())
            ->method('withQuery')
            ->with([
                'foo' => 'bar',
            ])
            ->willReturnSelf();

        $resource = $this->createMock(ResourceInterface::class);

        $identity = $this->createMock(IdentityInterface::class);

        $securityService = $this->createMock(SecurityServiceInterface::class);
        $securityService
            ->expects($this->once())
            ->method('getIdentity')
            ->willReturn($identity);

        $request = $this->createMock(RequestInterface::class);

        $request
            ->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $request
            ->expects($this->once())
            ->method('withUri')
            ->with($uri)
            ->willReturnSelf();

        $authorizer = $this->createMock(AuthorizerInterface::class);

        $expander = $this->createMock(ExpanderInterface::class);

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->once())
            ->method('serialize')
            ->with($resource)
            ->willReturn('{"id":1}');

        $builder = $this->createMock(BuilderInterface::class);
        $builder
            ->expects($this->once())
            ->method('setAuthorizer')
            ->with($authorizer)
            ->willReturnSelf();

        $builder
            ->expects($this->once())
            ->method('setExpander')
            ->with($expander)
            ->willReturnSelf();

        $builder
            ->expects($this->once())
            ->method('setIdentity')
            ->with($identity)
            ->willReturnSelf();

        $builder
            ->expects($this->once())
            ->method('setToExpand')
            ->with([
                'first'
            ])
            ->willReturnSelf();

        $builder
            ->expects($this->once())
            ->method('setToProject')
            ->with([
                'second'
            ])
            ->willReturnSelf();

        $builder
            ->expects($this->once())
            ->method('build')
            ->willReturn($resource);

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($builder);

        $response
            ->expects($this->exactly(2))
            ->method('withHeader')
            ->withConsecutive(
                ['Content-Type', 'application/hal+json'],
                ['Content-Length', '8']
            )
            ->willReturnSelf();

        $response
            ->expects($this->once())
            ->method('withBody')
            ->with('{"id":1}')
            ->willReturnSelf();

        $chain = $this->createMock(MiddlewareChainInterface::class);
        $chain
            ->expects($this->once())
            ->method('proceed')
            ->with($request)
            ->willReturn($response);

        /**
         * @var AuthorizerInterface $authorizer
         * @var ExpanderInterface $expander
         * @var SerializerInterface $serializer
         * @var RequestInterface $request
         * @var MiddlewareChainInterface $chain
         * @var ResponseInterface $response
         * @var SecurityServiceInterface $securityService
         */
        $middleware = new HateoasMiddleware($authorizer, $expander, $serializer, $securityService);

        $this->assertSame($response, $middleware->process($request, $chain));
    }

    /**
     * Test that correct response will be returned on LinkNotFound exception.
     *
     * @covers \ExtendsFramework\Hateoas\Framework\Http\Middleware\Hateoas\HateoasMiddleware::__construct()
     * @covers \ExtendsFramework\Hateoas\Framework\Http\Middleware\Hateoas\HateoasMiddleware::process()
     */
    public function testLinkNotFoundResponse(): void
    {
        $uri = $this->createMock(UriInterface::class);
        $uri
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn([
                'foo' => 'bar',
                'expand' => [
                    'first',
                ],
                'project' => [
                    'second',
                ],
            ]);

        $uri
            ->expects($this->once())
            ->method('withQuery')
            ->with([
                'foo' => 'bar',
            ])
            ->willReturnSelf();

        $identity = $this->createMock(IdentityInterface::class);

        $securityService = $this->createMock(SecurityServiceInterface::class);
        $securityService
            ->expects($this->once())
            ->method('getIdentity')
            ->willReturn($identity);

        $request = $this->createMock(RequestInterface::class);

        $request
            ->expects($this->exactly(2))
            ->method('getUri')
            ->willReturn($uri);

        $request
            ->expects($this->once())
            ->method('withUri')
            ->with($uri)
            ->willReturnSelf();

        $authorizer = $this->createMock(AuthorizerInterface::class);

        $expander = $this->createMock(ExpanderInterface::class);

        $serializer = $this->createMock(SerializerInterface::class);

        $builder = $this->createMock(BuilderInterface::class);
        $builder
            ->expects($this->once())
            ->method('setAuthorizer')
            ->with($authorizer)
            ->willReturnSelf();

        $builder
            ->expects($this->once())
            ->method('setExpander')
            ->with($expander)
            ->willReturnSelf();

        $builder
            ->expects($this->once())
            ->method('setIdentity')
            ->with($identity)
            ->willReturnSelf();

        $builder
            ->expects($this->once())
            ->method('setToExpand')
            ->with([
                'first'
            ])
            ->willReturnSelf();

        $builder
            ->expects($this->once())
            ->method('setToProject')
            ->with([
                'second'
            ])
            ->willReturnSelf();

        $builder
            ->expects($this->once())
            ->method('build')
            ->willThrowException(new LinkNotFound('author'));

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($builder);

        $chain = $this->createMock(MiddlewareChainInterface::class);
        $chain
            ->expects($this->once())
            ->method('proceed')
            ->with($request)
            ->willReturn($response);

        /**
         * @var AuthorizerInterface $authorizer
         * @var ExpanderInterface $expander
         * @var SerializerInterface $serializer
         * @var RequestInterface $request
         * @var MiddlewareChainInterface $chain
         * @var SecurityServiceInterface $securityService
         */
        $middleware = new HateoasMiddleware($authorizer, $expander, $serializer, $securityService);

        $response = $middleware->process($request, $chain);
        $this->assertInstanceOf(LinkNotFoundProblemDetails::class, $response->getBody());
    }

    /**
     * Test that correct response will be returned on LinkNotEmbeddable exception.
     *
     * @covers \ExtendsFramework\Hateoas\Framework\Http\Middleware\Hateoas\HateoasMiddleware::__construct()
     * @covers \ExtendsFramework\Hateoas\Framework\Http\Middleware\Hateoas\HateoasMiddleware::process()
     */
    public function testLinkNotEmbeddableResponse(): void
    {
        $uri = $this->createMock(UriInterface::class);
        $uri
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn([
                'foo' => 'bar',
                'expand' => [
                    'first',
                ],
                'project' => [
                    'second',
                ],
            ]);

        $uri
            ->expects($this->once())
            ->method('withQuery')
            ->with([
                'foo' => 'bar',
            ])
            ->willReturnSelf();

        $identity = $this->createMock(IdentityInterface::class);

        $securityService = $this->createMock(SecurityServiceInterface::class);
        $securityService
            ->expects($this->once())
            ->method('getIdentity')
            ->willReturn($identity);

        $request = $this->createMock(RequestInterface::class);

        $request
            ->expects($this->exactly(2))
            ->method('getUri')
            ->willReturn($uri);

        $request
            ->expects($this->once())
            ->method('withUri')
            ->with($uri)
            ->willReturnSelf();

        $authorizer = $this->createMock(AuthorizerInterface::class);

        $expander = $this->createMock(ExpanderInterface::class);

        $serializer = $this->createMock(SerializerInterface::class);

        $builder = $this->createMock(BuilderInterface::class);
        $builder
            ->expects($this->once())
            ->method('setAuthorizer')
            ->with($authorizer)
            ->willReturnSelf();

        $builder
            ->expects($this->once())
            ->method('setExpander')
            ->with($expander)
            ->willReturnSelf();

        $builder
            ->expects($this->once())
            ->method('setIdentity')
            ->with($identity)
            ->willReturnSelf();

        $builder
            ->expects($this->once())
            ->method('setToExpand')
            ->with([
                'first'
            ])
            ->willReturnSelf();

        $builder
            ->expects($this->once())
            ->method('setToProject')
            ->with([
                'second'
            ])
            ->willReturnSelf();

        $builder
            ->expects($this->once())
            ->method('build')
            ->willThrowException(new LinkNotEmbeddable('comments'));

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($builder);

        $chain = $this->createMock(MiddlewareChainInterface::class);
        $chain
            ->expects($this->once())
            ->method('proceed')
            ->with($request)
            ->willReturn($response);

        /**
         * @var AuthorizerInterface $authorizer
         * @var ExpanderInterface $expander
         * @var SerializerInterface $serializer
         * @var RequestInterface $request
         * @var MiddlewareChainInterface $chain
         * @var SecurityServiceInterface $securityService
         */
        $middleware = new HateoasMiddleware($authorizer, $expander, $serializer, $securityService);

        $response = $middleware->process($request, $chain);
        $this->assertInstanceOf(LinkNotEmbeddableProblemDetails::class, $response->getBody());
    }

    /**
     * Test that correct response will be returned on AttributeNotFound exception.
     *
     * @covers \ExtendsFramework\Hateoas\Framework\Http\Middleware\Hateoas\HateoasMiddleware::__construct()
     * @covers \ExtendsFramework\Hateoas\Framework\Http\Middleware\Hateoas\HateoasMiddleware::process()
     */
    public function testAttributeNotFoundResponse(): void
    {
        $uri = $this->createMock(UriInterface::class);
        $uri
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn([
                'foo' => 'bar',
                'expand' => [
                    'first',
                ],
                'project' => [
                    'second',
                ],
            ]);

        $uri
            ->expects($this->once())
            ->method('withQuery')
            ->with([
                'foo' => 'bar',
            ])
            ->willReturnSelf();

        $identity = $this->createMock(IdentityInterface::class);

        $securityService = $this->createMock(SecurityServiceInterface::class);
        $securityService
            ->expects($this->once())
            ->method('getIdentity')
            ->willReturn($identity);

        $request = $this->createMock(RequestInterface::class);

        $request
            ->expects($this->exactly(2))
            ->method('getUri')
            ->willReturn($uri);

        $request
            ->expects($this->once())
            ->method('withUri')
            ->with($uri)
            ->willReturnSelf();

        $authorizer = $this->createMock(AuthorizerInterface::class);

        $expander = $this->createMock(ExpanderInterface::class);

        $serializer = $this->createMock(SerializerInterface::class);

        $builder = $this->createMock(BuilderInterface::class);
        $builder
            ->expects($this->once())
            ->method('setAuthorizer')
            ->with($authorizer)
            ->willReturnSelf();

        $builder
            ->expects($this->once())
            ->method('setExpander')
            ->with($expander)
            ->willReturnSelf();

        $builder
            ->expects($this->once())
            ->method('setIdentity')
            ->with($identity)
            ->willReturnSelf();

        $builder
            ->expects($this->once())
            ->method('setToExpand')
            ->with([
                'first'
            ])
            ->willReturnSelf();

        $builder
            ->expects($this->once())
            ->method('setToProject')
            ->with([
                'second'
            ])
            ->willReturnSelf();

        $builder
            ->expects($this->once())
            ->method('build')
            ->willThrowException(new AttributeNotFound('name'));

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($builder);

        $chain = $this->createMock(MiddlewareChainInterface::class);
        $chain
            ->expects($this->once())
            ->method('proceed')
            ->with($request)
            ->willReturn($response);

        /**
         * @var AuthorizerInterface $authorizer
         * @var ExpanderInterface $expander
         * @var SerializerInterface $serializer
         * @var RequestInterface $request
         * @var MiddlewareChainInterface $chain
         * @var SecurityServiceInterface $securityService
         */
        $middleware = new HateoasMiddleware($authorizer, $expander, $serializer, $securityService);

        $response = $middleware->process($request, $chain);
        $this->assertInstanceOf(AttributeNotFoundProblemDetails::class, $response->getBody());
    }
}
