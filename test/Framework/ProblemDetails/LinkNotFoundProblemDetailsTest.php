<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Framework\ProblemDetails;

use ExtendsFramework\Hateoas\Builder\Exception\LinkNotFound;
use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\Http\Request\Uri\UriInterface;
use PHPUnit\Framework\TestCase;

class LinkNotFoundProblemDetailsTest extends TestCase
{
    /**
     * Test that getters will return correct values.
     *
     * @covers \ExtendsFramework\Hateoas\Framework\ProblemDetails\LinkNotFoundProblemDetails::__construct()
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

        $exception = $this->createMock(LinkNotFound::class);
        $exception
            ->expects($this->exactly(2))
            ->method('getRel')
            ->willReturn('author');

        /**
         * @var RequestInterface $request
         * @var LinkNotFound $exception
         */
        $problemDetails = new LinkNotFoundProblemDetails($request, $exception);

        $this->assertSame('/problems/hateoas/link-not-found', $problemDetails->getType());
        $this->assertSame('Link not found', $problemDetails->getTitle());
        $this->assertSame('Link with rel "author" can not be found.', $problemDetails->getDetail());
        $this->assertSame(404, $problemDetails->getStatus());
        $this->assertSame('/foo/bar', $problemDetails->getInstance());
        $this->assertSame(['rel' => 'author'], $problemDetails->getAdditional());
    }
}
