<?php
declare(strict_types=1);

namespace ExtendsFramework\Hateoas\Framework\ProblemDetails;

use ExtendsFramework\Hateoas\Builder\Exception\AttributeNotFound;
use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\ProblemDetails\ProblemDetails;

class AttributeNotFoundProblemDetails extends ProblemDetails
{
    /**
     * AttributeNotfoundProblemDetails constructor.
     *
     * @param RequestInterface $request
     * @param AttributeNotFound $exception
     */
    public function __construct(RequestInterface $request, AttributeNotFound $exception)
    {
        parent::__construct(
            '/problems/hateoas/attribute-not-found',
            'Attribute not found',
            sprintf(
                'Attribute with property "%s" can not be found.',
                $exception->getProperty()
            ),
            404,
            $request->getUri()->toRelative(),
            [
                'property' => $exception->getProperty(),
            ]
        );
    }
}
