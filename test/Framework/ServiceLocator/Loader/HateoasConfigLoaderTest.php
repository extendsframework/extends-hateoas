<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Framework\ServiceLocator\Loader;

use ExtendsFramework\Hateoas\Expander\Expander;
use ExtendsFramework\Hateoas\Expander\ExpanderInterface;
use ExtendsFramework\Hateoas\Framework\Http\Middleware\Hateoas\HateoasMiddleware;
use ExtendsFramework\Hateoas\Serializer\Json\JsonSerializer;
use ExtendsFramework\Hateoas\Serializer\SerializerInterface;
use ExtendsFramework\ServiceLocator\Resolver\Reflection\ReflectionResolver;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;

class HateoasConfigLoaderTest extends TestCase
{
    /**
     * Test that loader will return config.
     *
     * @covers \ExtendsFramework\Hateoas\Framework\ServiceLocator\Loader\HateoasConfigLoader::load()
     */
    public function testLoad(): void
    {
        $loader = new HateoasConfigLoader();

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->assertSame(
            [
                ServiceLocatorInterface::class => [
                    ReflectionResolver::class => [
                        SerializerInterface::class => JsonSerializer::class,
                        HateoasMiddleware::class => HateoasMiddleware::class,
                        ExpanderInterface::class => Expander::class,
                    ],
                ],
            ],
            $loader->load()
        );
    }
}
