<?php declare(strict_types=1);
namespace Onion\Framework\Http\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message;

/**
 * Class Delegate
 *
 * @package Onion\Framework\Http\Middleware
 */
final class Delegate implements DelegateInterface
{
    /**
     * @var MiddlewareInterface
     */
    protected $middleware;

    /**
     * @var Message\ResponseInterface
     */
    protected $response;

    /**
     * MiddlewareDelegate constructor.
     *
     * @param MiddlewareInterface[] $middleware Middleware of the frame
     */
    public function __construct(array $middleware, Message\ResponseInterface $response = null)
    {
        $this->middleware = $middleware;
        $this->response = $response;
    }

    /**
     * @param Message\ServerRequestInterface $request
     *
     * @throws \RuntimeException If asked to return the response template, but the template is empty
     * @return Message\ResponseInterface
     */
    public function process(Message\ServerRequestInterface $request): Message\ResponseInterface
    {
        if ($this->middleware !== []) {
            $middleware = array_shift($this->middleware);

            if ($middleware !== null) {
                assert(
                    $middleware instanceof MiddlewareInterface,
                    new \TypeError(
                        'All members of middleware must implement ServerMiddleware\MiddlewareInterface, '  .
                            gettype($middleware) . ': ' .
                            print_r($middleware, true) .
                            ' given'
                    )
                );

                return $middleware->process($request, $this);
            }
        }

        if (null === $this->response) {
            throw new \RuntimeException('No response template provided');
        }

        return $this->response;
    }
}
