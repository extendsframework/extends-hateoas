<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Link;

use ExtendsFramework\Authorization\Permission\PermissionInterface;
use ExtendsFramework\Http\Request\RequestInterface;

class Link implements LinkInterface
{
    /**
     * Request.
     *
     * @var RequestInterface
     */
    private $request;

    /**
     * If link is embeddable.
     *
     * @var bool
     */
    private $embeddable;

    /**
     * Permission.
     *
     * @var PermissionInterface|null
     */
    protected $permission;

    /**
     * Link constructor.
     *
     * @param RequestInterface $request
     * @param bool|null $embeddable
     * @param PermissionInterface|null $permission
     */
    public function __construct(
        RequestInterface $request,
        bool $embeddable = null,
        PermissionInterface $permission = null
    ) {
        $this->request = $request;
        $this->embeddable = $embeddable ?? false;
        $this->permission = $permission;
    }

    /**
     * @inheritDoc
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @inheritDoc
     */
    public function isEmbeddable(): bool
    {
        return $this->embeddable;
    }

    /**
     * @inheritDoc
     */
    public function getPermission(): ?PermissionInterface
    {
        return $this->permission;
    }
}
