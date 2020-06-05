<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Link;

use ExtendsFramework\Authorization\Permission\PermissionInterface;
use ExtendsFramework\Authorization\Policy\PolicyInterface;
use ExtendsFramework\Authorization\Role\RoleInterface;
use ExtendsFramework\Http\Request\RequestInterface;

interface LinkInterface
{
    /**
     * Get request.
     *
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface;

    /**
     * If link is embeddable.
     *
     * @return bool
     */
    public function isEmbeddable(): bool;

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
