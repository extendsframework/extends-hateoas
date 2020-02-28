<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Serializer\Json;

use ExtendsFramework\Hateoas\Attribute\AttributeInterface;
use ExtendsFramework\Hateoas\Link\LinkInterface;
use ExtendsFramework\Hateoas\ResourceInterface;
use ExtendsFramework\Hateoas\Serializer\SerializerInterface;
use ExtendsFramework\Router\RouterException;
use ExtendsFramework\Router\RouterInterface;

class JsonSerializer implements SerializerInterface
{
    /**
     * Router.
     *
     * @var RouterInterface
     */
    private $router;

    /**
     * JsonSerializer constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @inheritDoc
     * @throws RouterException
     */
    public function serialize(ResourceInterface $resource): string
    {
        return json_encode(
            $this->toArray($resource)
        );
    }

    /**
     * Serialize resource to array.
     *
     * @param ResourceInterface $resource
     *
     * @return array
     * @throws RouterException
     */
    private function toArray(ResourceInterface $resource): array
    {
        return array_merge(
            array_filter(
                [
                    '_links' => $this->serializeLinks($resource->getLinks()),
                    '_embedded' => $this->serializeResources($resource->getResources())
                ]
            ),
            $this->serializeAttributes($resource->getAttributes())
        );
    }

    /**
     * Serialize links.
     *
     * @param LinkInterface[] $links
     *
     * @return array
     * @throws RouterException
     */
    private function serializeLinks(array $links): array
    {
        $serialized = [];
        foreach ($links as $rel => $link) {
            if (is_array($link)) {
                $serialized[$rel] = $this->serializeLinks($link);
            } else {
                $serialized[$rel] = [
                    'href' => $link
                        ->getRequest()
                        ->getUri()
                        ->toRelative(),
                ];
            }
        }

        return $serialized;
    }

    /**
     * Serialize embedded resources.
     *
     * @param ResourceInterface[] $resources
     *
     * @return array
     * @throws RouterException
     */
    private function serializeResources(array $resources): array
    {
        $serialized = [];
        foreach ($resources as $rel => $resource) {
            if (is_array($resource)) {
                $serialized[$rel] = array_values($this->serializeResources($resource));
            } else {
                $serialized[$rel] = $this->toArray($resource);
            }
        }

        return $serialized;
    }

    /**
     * Serialize attributes.
     *
     * @param AttributeInterface[] $attributes
     *
     * @return array
     */
    private function serializeAttributes(array $attributes): array
    {
        $serialized = [];
        foreach ($attributes as $property => $attribute) {
            $serialized[$property] = $attribute->getValue();
        }

        return $serialized;
    }
}
