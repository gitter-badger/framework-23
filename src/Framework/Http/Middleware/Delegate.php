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
     * MiddlewareDelegate constructor.
     *
     * @param ServerMiddlewareInterface[] $middleware Middleware of the frame
     */
    public function __construct(array $middleware)
    {
        $this->middleware = $middleware;
    }

    /**
     * @param Message\RequestInterface $request
     *
     * @throws Exceptions\MiddlewareException if returned response is not instance of ResponseInterface
     * @return Message\ResponseInterface
     */
    public function process(Message\RequestInterface $request): Message\ResponseInterface
    {
        if ($this->middleware !== []) {
            $middleware = array_shift($this->middleware);

            return $middleware->process($request, $this);
        }

        return new \Zend\Diactoros\Response();
    }
}
