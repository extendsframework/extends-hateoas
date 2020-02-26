<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Builder;

use ExtendsFramework\Authorization\AuthorizerInterface;
use ExtendsFramework\Hateoas\Attribute\AttributeInterface;
use ExtendsFramework\Hateoas\Builder\Exception\AttributeNotFound;
use ExtendsFramework\Hateoas\Builder\Exception\LinkNotEmbeddable;
use ExtendsFramework\Hateoas\Builder\Exception\LinkNotFound;
use ExtendsFramework\Hateoas\Expander\ExpanderInterface;
use ExtendsFramework\Hateoas\Link\LinkInterface;
use ExtendsFramework\Hateoas\ResourceInterface;
use ExtendsFramework\Identity\IdentityInterface;

interface BuilderInterface
{
    /**
     * Add link.
     *
     * @param string $rel
     * @param LinkInterface $link
     * @param bool|null $singular
     *
     * @return Builder
     */
    public function addLink(string $rel, LinkInterface $link, bool $singular = null): BuilderInterface;

    /**
     * Add attribute.
     *
     * @param string $property
     * @param AttributeInterface $attribute
     *
     * @return Builder
     */
    public function addAttribute(string $property, AttributeInterface $attribute): BuilderInterface;

    /**
     * Add resource.
     *
     * @param string $rel
     * @param BuilderInterface $resource
     * @param bool|null $singular
     *
     * @return Builder
     */
    public function addResource(string $rel, BuilderInterface $resource, bool $singular = null): BuilderInterface;

    /**
     * Set links rels to embed.
     *
     * @param string[]|string[][]|null $rels
     *
     * @return BuilderInterface
     */
    public function setToExpand(array $rels = null): BuilderInterface;

    /**
     * Set properties to project.
     *
     * @param string[]|string[][]|null $properties
     *
     * @return BuilderInterface
     */
    public function setToProject(array $properties = null): BuilderInterface;

    /**
     * Set identity to use for authorization.
     *
     * @param IdentityInterface|null $identity
     *
     * @return BuilderInterface
     */
    public function setIdentity(IdentityInterface $identity = null): BuilderInterface;

    /**
     * Set authorizer.
     *
     * @param AuthorizerInterface|null $authorizer
     *
     * @return BuilderInterface
     */
    public function setAuthorizer(AuthorizerInterface $authorizer = null): BuilderInterface;

    /**
     * Set resource expander.
     *
     * @param ExpanderInterface|null $expander
     *
     * @return BuilderInterface
     */
    public function setExpander(ExpanderInterface $expander = null): BuilderInterface;

    /**
     * Build resource.
     *
     * @return ResourceInterface
     * @throws LinkNotFound
     * @throws LinkNotEmbeddable
     * @throws AttributeNotFound
     */
    public function build(): ResourceInterface;
}
