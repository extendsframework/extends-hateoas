<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Framework\ServiceLocator\Loader;

use ExtendsFramework\Hateoas\Expander\Expander;
use ExtendsFramework\Hateoas\Expander\ExpanderInterface;
use ExtendsFramework\Hateoas\Framework\Http\Middleware\Hateoas\HateoasMiddleware;
use ExtendsFramework\Hateoas\Serializer\Json\JsonSerializer;
use ExtendsFramework\Hateoas\Serializer\SerializerInterface;
use ExtendsFramework\ServiceLocator\Config\Loader\LoaderInterface;
use ExtendsFramework\ServiceLocator\Resolver\Reflection\ReflectionResolver;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;

class HateoasConfigLoader implements LoaderInterface
{
    /**
     * @inheritDoc
     */
    public function load(): array
    {
        return [
            ServiceLocatorInterface::class => [
                ReflectionResolver::class => [
                    SerializerInterface::class => JsonSerializer::class,
                    HateoasMiddleware::class => HateoasMiddleware::class,
                    ExpanderInterface::class => Expander::class,
                ],
            ],
        ];
    }
}
