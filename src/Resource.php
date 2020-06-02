<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas;

use ExtendsFramework\Hateoas\Attribute\AttributeInterface;
use ExtendsFramework\Hateoas\Link\LinkInterface;

class Resource implements ResourceInterface
{
    /**
     * Links.
     *
     * @var LinkInterface[]
     */
    private $links;

    /**
     * Attributes.
     *
     * @var AttributeInterface[]
     */
    private $attributes;

    /**
     * Embedded resources.
     *
     * @var ResourceInterface[]
     */
    private $resources;

    /**
     * Resource constructor.
     *
     * @param LinkInterface[] $links
     * @param AttributeInterface[] $attributes
     * @param ResourceInterface[]|null $resources
     */
    public function __construct(array $links, array $attributes, array $resources = null)
    {
        $this->links = $links;
        $this->attributes = $attributes;
        $this->resources = $resources ?? [];
    }

    /**
     * @inheritDoc
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @inheritDoc
     */
    public function getResources(): array
    {
        return $this->resources;
    }
}
