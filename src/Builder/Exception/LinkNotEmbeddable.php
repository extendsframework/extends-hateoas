<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Builder\Exception;

use Exception;
use ExtendsFramework\Hateoas\Builder\BuilderException;

class LinkNotEmbeddable extends Exception implements BuilderException
{
    /**
     * Link rel.
     *
     * @var string
     */
    private $rel;

    /**
     * LinkNotEmbeddable constructor.
     *
     * @param string $property
     */
    public function __construct(string $property)
    {
        $this->rel = $property;

        parent::__construct(sprintf(
            'Link with rel "%s" is not embeddable.',
            $property
        ));
    }

    /**
     * Get link rel.
     *
     * @return string
     */
    public function getRel(): string
    {
        return $this->rel;
    }
}
