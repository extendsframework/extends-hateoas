<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Framework\ProblemDetails;

use ExtendsFramework\Hateoas\Builder\Exception\LinkNotEmbeddable;
use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\ProblemDetails\ProblemDetails;

class LinkNotEmbeddableProblemDetails extends ProblemDetails
{
    /**
     * LinkNotfoundProblemDetails constructor.
     *
     * @param RequestInterface $request
     * @param LinkNotEmbeddable $exception
     */
    public function __construct(RequestInterface $request, LinkNotEmbeddable $exception)
    {
        parent::__construct(
            '/problems/hateoas/link-not-embeddable',
            'Link not embeddable',
            sprintf(
                'Link with rel "%s" is not embeddable.',
                $exception->getRel()
            ),
            400,
            $request->getUri()->toRelative(),
            [
                'rel' => $exception->getRel(),
            ]
        );
    }
}
