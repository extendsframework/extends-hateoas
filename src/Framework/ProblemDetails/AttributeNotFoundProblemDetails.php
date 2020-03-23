<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Framework\ProblemDetails;

use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\ProblemDetails\ProblemDetails;

class AttributeNotFoundProblemDetails extends ProblemDetails
{
    /**
     * AttributeNotfoundProblemDetails constructor.
     *
     * @param RequestInterface $request
     * @param string $property
     */
    public function __construct(RequestInterface $request, string $property)
    {
        parent::__construct(
            '/problems/hateoas/attribute-not-found',
            'Attribute not found',
            sprintf(
                'Attribute with property "%s" can not be found.',
                $property
            ),
            404,
            $request->getUri()->toRelative(),
            [
                'property' => $property,
            ]
        );
    }
}
