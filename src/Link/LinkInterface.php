<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Link;

use ExtendsFramework\Authorization\Permission\PermissionInterface;
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
     * Get permission.
     *
     * @return PermissionInterface|null
     */
    public function getPermission(): ?PermissionInterface;
}
