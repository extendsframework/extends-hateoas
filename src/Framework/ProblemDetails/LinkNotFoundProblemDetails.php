<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Framework\ProblemDetails;

use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\ProblemDetails\ProblemDetails;

class LinkNotFoundProblemDetails extends ProblemDetails
{
    /**
     * LinkNotfoundProblemDetails constructor.
     *
     * @param RequestInterface $request
     * @param string $rel
     */
    public function __construct(RequestInterface $request, string $rel)
    {
        parent::__construct(
            '/problems/hateoas/link-not-found',
            'Link not found',
            sprintf(
                'Link with rel "%s" can not be found.',
                $rel
            ),
            404,
            $request->getUri()->toRelative(),
            [
                'rel' => $rel,
            ]
        );
    }
}
