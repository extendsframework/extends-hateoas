<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Serializer;

use ExtendsFramework\Hateoas\ResourceInterface;

interface SerializerInterface
{
    /**
     * Serialize resource.
     *
     * @param ResourceInterface $resource
     *
     * @return string
     */
    public function serialize(ResourceInterface $resource): string;
}
