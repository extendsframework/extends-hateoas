<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Attribute;

use ExtendsFramework\Authorization\Permission\PermissionInterface;
use ExtendsFramework\Authorization\Policy\PolicyInterface;
use ExtendsFramework\Authorization\Role\RoleInterface;

class Attribute implements AttributeInterface
{
    /**
     * Value.
     *
     * @var mixed
     */
    private $value;

    /**
     * Role.
     *
     * @var RoleInterface
     */
    private $role;

    /**
     * Permission.
     *
     * @var PermissionInterface|null
     */
    private $permission;

    /**
     * Policy.
     *
     * @var PolicyInterface
     */
    private $policy;

    /**
     * Attribute constructor.
     *
     * @param mixed $value
     * @param RoleInterface|null $role
     * @param PermissionInterface|null $permission
     * @param PolicyInterface|null $policy
     */
    public function __construct(
        $value,
        RoleInterface $role = null,
        PermissionInterface $permission = null,
        PolicyInterface $policy = null
    ) {
        $this->value = $value;
        $this->role = $role;
        $this->permission = $permission;
        $this->policy = $policy;
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
    public function getRole(): ?RoleInterface
    {
        return $this->role;
    }

    /**
     * @inheritDoc
     */
    public function getPermission(): ?PermissionInterface
    {
        return $this->permission;
    }

    /**
     * @inheritDoc
     */
    public function getPolicy(): ?PolicyInterface
    {
        return $this->policy;
    }
}
