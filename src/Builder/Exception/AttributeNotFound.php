<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Builder\Exception;

use Exception;
use ExtendsFramework\Hateoas\Builder\BuilderException;

class AttributeNotFound extends Exception implements BuilderException
{
    /**
     * Attribute property.
     *
     * @var string
     */
    private $property;

    /**
     * AttributeNotFound constructor.
     *
     * @param string $property
     */
    public function __construct(string $property)
    {
        $this->property = $property;

        parent::__construct(sprintf(
            'Attribute with property "%s" does not exists.',
            $property
        ));
    }

    /**
     * Get attribute property.
     *
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }
}
