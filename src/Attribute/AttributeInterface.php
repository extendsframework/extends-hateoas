<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Attribute;

use ExtendsFramework\Authorization\Permission\PermissionInterface;

interface AttributeInterface
{
    /**
     * Get value.
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Get permission.
     *
     * @return PermissionInterface|null
     */
    public function getPermission(): ?PermissionInterface;
}
