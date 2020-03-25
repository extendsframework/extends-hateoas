<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Framework\ProblemDetails;

use ExtendsFramework\Hateoas\Builder\Exception\LinkNotEmbeddable;
use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\Http\Request\Uri\UriInterface;
use ExtendsFramework\ProblemDetails\ProblemDetailsInterface;
use PHPUnit\Framework\TestCase;

class LinkNotEmbeddableProblemDetailsTest extends TestCase
{
    /**
     * Test that getters will return correct values.
     *
     * @covers \ExtendsFramework\Hateoas\Framework\ProblemDetails\LinkNotEmbeddableProblemDetails::__construct()
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

        $exception = $this->createMock(LinkNotEmbeddable::class);
        $exception
            ->expects($this->exactly(2))
            ->method('getRel')
            ->willReturn('comments');

        /**
         * @var RequestInterface $request
         * @var LinkNotEmbeddable $exception
         */
        $problemDetails = new LinkNotEmbeddableProblemDetails($request, $exception);

        $this->assertInstanceOf(ProblemDetailsInterface::class, $problemDetails);
        $this->assertSame('/problems/hateoas/link-not-embeddable', $problemDetails->getType());
        $this->assertSame('Link not embeddable', $problemDetails->getTitle());
        $this->assertSame('Link with rel "comments" is not embeddable.', $problemDetails->getDetail());
        $this->assertSame(400, $problemDetails->getStatus());
        $this->assertSame('/foo/bar', $problemDetails->getInstance());
        $this->assertSame(['rel' => 'comments'], $problemDetails->getAdditional());
    }
}
