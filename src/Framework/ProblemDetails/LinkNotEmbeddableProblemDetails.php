<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Framework\ProblemDetails;

use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\ProblemDetails\ProblemDetails;

class LinkNotEmbeddableProblemDetails extends ProblemDetails
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
            '/problems/hateoas/link-not-embeddable',
            'Link not embeddable',
            sprintf(
                'Link with rel "%s" is not embeddable.',
                $rel
            ),
            400,
            $request->getUri()->toRelative(),
            [
                'rel' => $rel,
            ]
        );
    }
}
