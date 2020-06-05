<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Attribute;

use ExtendsFramework\Authorization\Permission\PermissionInterface;
use ExtendsFramework\Authorization\Policy\PolicyInterface;
use ExtendsFramework\Authorization\Role\RoleInterface;

interface AttributeInterface
{
    /**
     * Get value.
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Get role.
     *
     * @return RoleInterface|null
     */
    public function getRole(): ?RoleInterface;

    /**
     * Get permission.
     *
     * @return PermissionInterface|null
     */
    public function getPermission(): ?PermissionInterface;

    /**
     * Get policy.
     *
     * @return PolicyInterface|null
     */
    public function getPolicy(): ?PolicyInterface;
}
