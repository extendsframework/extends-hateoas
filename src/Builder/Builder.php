<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Builder;

use ExtendsFramework\Authorization\AuthorizerInterface;
use ExtendsFramework\Authorization\Permission\PermissionInterface;
use ExtendsFramework\Authorization\Policy\PolicyInterface;
use ExtendsFramework\Authorization\Role\RoleInterface;
use ExtendsFramework\Hateoas\Attribute\AttributeInterface;
use ExtendsFramework\Hateoas\Builder\Exception\AttributeNotFound;
use ExtendsFramework\Hateoas\Builder\Exception\LinkNotEmbeddable;
use ExtendsFramework\Hateoas\Builder\Exception\LinkNotFound;
use ExtendsFramework\Hateoas\Expander\ExpanderException;
use ExtendsFramework\Hateoas\Expander\ExpanderInterface;
use ExtendsFramework\Hateoas\Link\LinkInterface;
use ExtendsFramework\Hateoas\Resource;
use ExtendsFramework\Hateoas\ResourceInterface;
use ExtendsFramework\Identity\IdentityInterface;

class Builder implements BuilderInterface
{
    /**
     * Authorizer.
     *
     * @var AuthorizerInterface|null
     */
    private $authorizer;

    /**
     * Resource expander.
     *
     * @var ExpanderInterface|null
     */
    private $expander;

    /**
     * Identity.
     *
     * @var IdentityInterface|null
     */
    private $identity;

    /**
     * Links.
     *
     * @var LinkInterface[]|LinkInterface[][]
     */
    private $links = [];

    /**
     * Attributes.
     *
     * @var AttributeInterface[]|AttributeInterface[][]
     */
    private $attributes = [];

    /**
     * Resources.
     *
     * @var BuilderInterface[]|BuilderInterface[][]
     */
    private $resources = [];

    /**
     * Embeddable link rels to expand in resource.
     *
     * @var string[]
     */
    private $toExpand = [];

    /**
     * Attributes properties to project in resource.
     *
     * @var string[]
     */
    private $toProject = [];

    /**
     * @inheritDoc
     */
    public function addLink(string $rel, LinkInterface $link, bool $singular = null): BuilderInterface
    {
        if ($singular ?? true) {
            $this->links[$rel] = $link;
        } else {
            $this->links[$rel][] = $link;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addAttribute(string $property, AttributeInterface $attribute): BuilderInterface
    {
        $this->attributes[$property] = $attribute;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addResource(string $rel, BuilderInterface $resource, bool $singular = null): BuilderInterface
    {
        if ($singular ?? true) {
            $this->resources[$rel] = $resource;
        } else {
            $this->resources[$rel][] = $resource;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setToExpand(array $rels = null): BuilderInterface
    {
        $this->toExpand = $rels ?? [];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setToProject(array $properties = null): BuilderInterface
    {
        $this->toProject = $properties ?? [];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setIdentity(IdentityInterface $identity = null): BuilderInterface
    {
        $this->identity = $identity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setAuthorizer(AuthorizerInterface $authorizer = null): BuilderInterface
    {
        $this->authorizer = $authorizer;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setExpander(ExpanderInterface $expander = null): BuilderInterface
    {
        $this->expander = $expander;

        return $this;
    }

    /**
     * @inheritDoc
     * @throws ExpanderException
     * @throws AttributeNotFound
     * @throws LinkNotFound
     * @throws LinkNotEmbeddable
     */
    public function build(): ResourceInterface
    {
        $links = $this->getAuthorizedLinks($this->links);

        $resource = new Resource(
            $links,
            $this->getProjectedAttributes(
                $this->getAuthorizedAttributes($this->attributes),
                array_filter($this->toProject, 'is_string')
            ),
            $this->getBuiltResources(
                array_merge(
                    $this->resources,
                    $this->getExpandedResources($links, array_filter($this->toExpand, 'is_string'))
                )
            )
        );

        $this->reset();

        return $resource;
    }

    /**
     * Get permitted links.
     *
     * @param LinkInterface[]|LinkInterface[][] $links
     *
     * @return LinkInterface[]|LinkInterface[][]
     */
    private function getAuthorizedLinks(array $links): array
    {
        $authorized = [];
        foreach ($links as $rel => $link) {
            if (is_array($link)) {
                $authorized[$rel] = $this->getAuthorizedLinks($link);
            } else {
                if ($this->isAuthorized($link->getRole(), $link->getPermission(), $link->getPolicy())) {
                    $authorized[$rel] = $link;
                }
            }
        }

        return $authorized;
    }

    /**
     * Check if authorized.
     *
     * @param RoleInterface|null $role
     * @param PermissionInterface|null $permission
     * @param PolicyInterface|null $policy
     *
     * @return bool
     */
    private function isAuthorized(
        RoleInterface $role = null,
        PermissionInterface $permission = null,
        PolicyInterface $policy = null
    ): bool {
        $authorized = true;
        if ($role || $permission || $policy) {
            $authorized = false;

            if ($this->authorizer && $this->identity) {
                if (($role && $this->authorizer->hasRole($this->identity, $role)) ||
                    ($permission && $this->authorizer->isPermitted($this->identity, $permission)) ||
                    ($policy && $this->authorizer->isAllowed($this->identity, $policy))
                ) {
                    $authorized = true;
                }
            }
        }

        return $authorized;
    }

    /**
     * Get projected attributes.
     *
     * @param AttributeInterface[] $attributes
     * @param string[] $properties
     *
     * @return AttributeInterface[]
     * @throws AttributeNotFound
     */
    private function getProjectedAttributes(array $attributes, array $properties): array
    {
        $projected = [];
        foreach ($properties as $property) {
            if (!isset($attributes[$property])) {
                throw new AttributeNotFound($property);
            }

            $projected[$property] = $attributes[$property];
        }

        return $projected ?: $attributes;
    }

    /**
     * Get permitted attributes.
     *
     * @param AttributeInterface[] $attributes
     *
     * @return AttributeInterface[]
     */
    private function getAuthorizedAttributes(array $attributes): array
    {
        $authorized = [];
        foreach ($attributes as $property => $attribute) {
            if ($this->isAuthorized($attribute->getRole(), $attribute->getPermission(), $attribute->getPolicy())) {
                $authorized[$property] = $attribute;
            }
        }

        return $authorized;
    }

    /**
     * Build built resources.
     *
     * @param BuilderInterface[]|BuilderInterface[][] $resources
     * @param string|null $outerRel
     *
     * @return ResourceInterface[]|ResourceInterface[][]
     * @throws AttributeNotFound
     * @throws LinkNotEmbeddable
     * @throws LinkNotFound
     */
    private function getBuiltResources(array $resources, string $outerRel = null): array
    {
        $build = [];
        foreach ($resources as $rel => $resource) {
            if (is_array($resource)) {
                $build[$rel] = $this->getBuiltResources($resource, $rel);
            } else {
                $build[$rel] = $resource
                    ->setIdentity($this->identity)
                    ->setAuthorizer($this->authorizer)
                    ->setExpander($this->expander)
                    ->setToExpand($this->toExpand[$outerRel ?: $rel] ?? [])
                    ->setToProject($this->toProject[$outerRel ?: $rel] ?? [])
                    ->build();
            }
        }

        return $build;
    }

    /**
     * Get expanded resources.
     *
     * @param LinkInterface[]|LinkInterface[][] $links
     * @param string[] $rels
     *
     * @return BuilderInterface[]
     * @throws ExpanderException
     * @throws LinkNotFound
     * @throws LinkNotEmbeddable
     */
    private function getExpandedResources(array $links, array $rels): array
    {
        $expanded = [];
        foreach ($rels as $rel) {
            if (!isset($links[$rel])) {
                throw new LinkNotFound($rel);
            }

            $link = $links[$rel];
            if (!$link->isEmbeddable()) {
                throw new LinkNotEmbeddable($rel);
            }

            $expanded[$rel] = $this->expander->expand($links[$rel]);
        }

        return $expanded;
    }

    /**
     * Reset the builder.
     *
     * @return void
     */
    private function reset(): void
    {
        $this->authorizer = null;
        $this->expander = null;
        $this->identity = null;
        $this->links = [];
        $this->attributes = [];
        $this->resources = [];
        $this->toExpand = [];
        $this->toProject = [];
    }
}
