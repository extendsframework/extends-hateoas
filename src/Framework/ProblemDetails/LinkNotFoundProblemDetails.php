<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Framework\ProblemDetails;

use ExtendsFramework\Hateoas\Builder\Exception\LinkNotFound;
use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\ProblemDetails\ProblemDetails;

class LinkNotFoundProblemDetails extends ProblemDetails
{
    /**
     * LinkNotfoundProblemDetails constructor.
     *
     * @param RequestInterface $request
     * @param LinkNotFound $exception
     */
    public function __construct(RequestInterface $request, LinkNotFound $exception)
    {
        parent::__construct(
            '/problems/hateoas/link-not-found',
            'Link not found',
            sprintf(
                'Link with rel "%s" can not be found.',
                $exception->getRel()
            ),
            404,
            $request->getUri()->toRelative(),
            [
                'rel' => $exception->getRel(),
            ]
        );
    }
}
