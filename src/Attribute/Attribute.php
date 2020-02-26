<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Attribute;

use ExtendsFramework\Authorization\Permission\PermissionInterface;

class Attribute implements AttributeInterface
{
    /**
     * Value.
     *
     * @var mixed
     */
    private $value;

    /**
     * Permission.
     *
     * @var PermissionInterface|null
     */
    private $permission;

    /**
     * Attribute constructor.
     *
     * @param mixed $value
     * @param PermissionInterface|null $permission
     */
    public function __construct($value, PermissionInterface $permission = null)
    {
        $this->value = $value;
        $this->permission = $permission;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function getPermission(): ?PermissionInterface
    {
        return $this->permission;
    }
}
