<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Expander;

use ExtendsFramework\Hateoas\Builder\BuilderInterface;
use ExtendsFramework\Hateoas\Link\LinkInterface;

interface ExpanderInterface
{
    /**
     * Expand resource from link.
     *
     * @param LinkInterface $link
     *
     * @return BuilderInterface
     * @throws ExpanderException
     */
    public function expand(LinkInterface $link): BuilderInterface;
}
