<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Expander;

use ExtendsFramework\Hateoas\Builder\BuilderInterface;
use ExtendsFramework\Hateoas\Link\LinkInterface;
use ExtendsFramework\Router\Controller\Executor\ExecutorException;
use ExtendsFramework\Router\Controller\Executor\ExecutorInterface;
use ExtendsFramework\Router\RouterException;
use ExtendsFramework\Router\RouterInterface;

class Expander implements ExpanderInterface
{
    /**
     * Router.
     *
     * @var RouterInterface
     */
    private $router;

    /**
     * Controller executor.
     *
     * @var ExecutorInterface
     */
    private $executor;

    /**
     * Expander constructor.
     *
     * @param RouterInterface $router
     * @param ExecutorInterface $executor
     */
    public function __construct(RouterInterface $router, ExecutorInterface $executor)
    {
        $this->router = $router;
        $this->executor = $executor;
    }

    /**
     * @inheritDoc
     * @throws RouterException
     * @throws ExecutorException
     */
    public function expand(LinkInterface $link): BuilderInterface
    {
        $request = $link->getRequest();

        $match = $this->router->route($request);
        $response = $this->executor->execute($request, $match);

        return $response->getBody();
    }
}
