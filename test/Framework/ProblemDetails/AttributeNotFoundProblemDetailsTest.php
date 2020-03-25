<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Framework\ProblemDetails;

use ExtendsFramework\Hateoas\Builder\Exception\AttributeNotFound;
use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\Http\Request\Uri\UriInterface;
use PHPUnit\Framework\TestCase;

class AttributeNotFoundProblemDetailsTest extends TestCase
{
    /**
     * Test that getters will return correct values.
     *
     * @covers \ExtendsFramework\Hateoas\Framework\ProblemDetails\AttributeNotFoundProblemDetails::__construct()
     */
    public function testGetters(): void
    {
        $uri = $this->createMock(UriInterface::class);
        $uri
            ->expects($this->once())
            ->method('toRelative')
            ->willReturn('/foo/bar');

        $request = $this->createMock(RequestInterface::class);
        $request
            ->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $exception = $this->createMock(AttributeNotFound::class);
        $exception
            ->expects($this->exactly(2))
            ->method('getProperty')
            ->willReturn('author');

        /**
         * @var RequestInterface $request
         * @var AttributeNotFound $exception
         */
        $problemDetails = new AttributeNotFoundProblemDetails($request, $exception);

        $this->assertSame('/problems/hateoas/attribute-not-found', $problemDetails->getType());
        $this->assertSame('Attribute not found', $problemDetails->getTitle());
        $this->assertSame('Attribute with property "author" can not be found.', $problemDetails->getDetail());
        $this->assertSame(404, $problemDetails->getStatus());
        $this->assertSame('/foo/bar', $problemDetails->getInstance());
        $this->assertSame(['property' => 'author'], $problemDetails->getAdditional());
    }
}
