<?php
declare(strict_types=1);
namespace Onion\Framework\Http\Middleware;

use Interop\Http\Middleware\DelegateInterface;
use Interop\Http\Middleware\ServerMiddlewareInterface;
use Psr\Http\Message;

final class Delegate implements DelegateInterface
{
    /**
     * @var ServerMiddlewareInterface
     */
    protected $middleware;

    /**
     * @var Message\ResponseInterface
     */
    protected $response;

    /**
     * MiddlewareDelegate constructor.
     *
     * @param ServerMiddlewareInterface[] $middleware Middleware of the frame
     */
    public function __construct(array $middleware, Message\ResponseInterface $response = null)
    {
        $this->middleware = $middleware;
        $this->response = $response;
    }

    /**
     * @param Message\RequestInterface $request
     *
     * @throws Exceptions\MiddlewareException if returned response is not instance of ResponseInterface
     * @return Message\ResponseInterface
     */
    public function process(Message\ServerRequestInterface $request): Message\ResponseInterface
    {
        if ($this->middleware !== []) {
            $middleware = array_shift($this->middleware);

            if ($middleware !== null) {
                if (!$middleware instanceof ServerMiddlewareInterface) {
                    throw new \TypeError(
                        'All members of middleware must implement ServerMiddlewareInterface'
                    );
                }

                return $middleware->process($request, $this);
            }
        }

        if (null === $this->response) {
            throw new \RuntimeException('No response template provide');
        }

        return $this->response;
    }
}
