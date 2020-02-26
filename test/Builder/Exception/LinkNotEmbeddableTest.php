<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Builder\Exception;

use PHPUnit\Framework\TestCase;

class LinkNotEmbeddableTest extends TestCase
{
    /**
     * Test that get methods will return correct values.
     *
     * @covers \ExtendsFramework\Hateoas\Builder\Exception\LinkNotEmbeddable::__construct()
     * @covers \ExtendsFramework\Hateoas\Builder\Exception\LinkNotEmbeddable::getRel()
     */
    public function testGetMethods(): void
    {
        $exception = new LinkNotEmbeddable('comments');

        $this->assertSame('Link with rel "comments" is not embeddable.', $exception->getMessage());
        $this->assertSame('comments', $exception->getRel());
    }
}
