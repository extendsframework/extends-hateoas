<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Framework\Http\Middleware\Hateoas;

use ExtendsFramework\Authorization\AuthorizerInterface;
use ExtendsFramework\Hateoas\Builder\BuilderInterface;
use ExtendsFramework\Hateoas\Builder\Exception\AttributeNotFound;
use ExtendsFramework\Hateoas\Builder\Exception\LinkNotEmbeddable;
use ExtendsFramework\Hateoas\Builder\Exception\LinkNotFound;
use ExtendsFramework\Hateoas\Expander\ExpanderInterface;
use ExtendsFramework\Hateoas\Framework\ProblemDetails\AttributeNotFoundProblemDetails;
use ExtendsFramework\Hateoas\Framework\ProblemDetails\LinkNotEmbeddableProblemDetails;
use ExtendsFramework\Hateoas\Framework\ProblemDetails\LinkNotFoundProblemDetails;
use ExtendsFramework\Hateoas\Serializer\SerializerInterface;
use ExtendsFramework\Http\Middleware\Chain\MiddlewareChainInterface;
use ExtendsFramework\Http\Middleware\MiddlewareInterface;
use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\Http\Response\Response;
use ExtendsFramework\Http\Response\ResponseInterface;

class HateoasMiddleware implements MiddlewareInterface
{
    /**
     * Authorizer.
     *
     * @var AuthorizerInterface
     */
    private $authorizer;

    /**
     * Resource expander.
     *
     * @var ExpanderInterface
     */
    private $expander;

    /**
     * Serializer.
     *
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * HateoasMiddleware constructor.
     *
     * @param AuthorizerInterface $authorizer
     * @param ExpanderInterface $expander
     * @param SerializerInterface $serializer
     */
    public function __construct(
        AuthorizerInterface $authorizer,
        ExpanderInterface $expander,
        SerializerInterface $serializer
    ) {
        $this->authorizer = $authorizer;
        $this->expander = $expander;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function process(RequestInterface $request, MiddlewareChainInterface $chain): ResponseInterface
    {
        $clonedRequest = clone $request;

        $uri = $request->getUri();
        $query = $uri->getQuery();

        $expand = $query['expand'] ?? null;
        $project = $query['project'] ?? null;

        unset($query['expand'], $query['project']);
        $request = $request->withUri($uri->withQuery($query));

        $response = $chain->proceed($request);
        $builder = $response->getBody();
        if ($builder instanceof BuilderInterface) {
            try {
                $serialized = $this
                    ->serializer
                    ->serialize(
                        $builder
                            ->setExpander($this->expander)
                            ->setAuthorizer($this->authorizer)
                            ->setIdentity($request->getAttribute('identity'))
                            ->setToExpand($expand)
                            ->setToProject($project)
                            ->build()
                    );

                $response = $response
                    ->withHeader('Content-Type', 'application/hal+json')
                    ->withHeader('Content-Length', (string)strlen($serialized))
                    ->withBody($serialized);
            } catch (LinkNotFound $exception) {
                return (new Response())->withBody(
                    new LinkNotFoundProblemDetails($clonedRequest, $exception->getRel())
                );
            } catch (LinkNotEmbeddable $exception) {
                return (new Response())->withBody(
                    new LinkNotEmbeddableProblemDetails($clonedRequest, $exception->getRel())
                );
            } catch (AttributeNotFound $exception) {
                return (new Response())->withBody(
                    new AttributeNotFoundProblemDetails($clonedRequest, $exception->getProperty())
                );
            }
        }

        return $response;
    }
}
