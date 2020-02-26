<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Builder\Exception;

use Exception;
use ExtendsFramework\Hateoas\Builder\BuilderException;

class LinkNotFound extends Exception implements BuilderException
{
    /**
     * Link rel.
     *
     * @var string
     */
    private $rel;

    /**
     * LinkNotFound constructor.
     *
     * @param string $property
     */
    public function __construct(string $property)
    {
        $this->rel = $property;

        parent::__construct(sprintf(
            'Link with rel "%s" does not exists.',
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
