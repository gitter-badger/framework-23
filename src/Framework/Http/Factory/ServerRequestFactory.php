<?php
declare(strict_types=1);
namespace Onion\Framework\Http\Factory;

use Onion\Framework\Dependency\Interfaces\FactoryInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ServerRequestFactory
 *
 * @package Onion\Framework\Http\Factory
 */
final class ServerRequestFactory implements FactoryInterface
{
    /**
     * Method that handles the construction of the object
     *
     * @param ContainerInterface $container DI Container
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     * @throws \InvalidArgumentException
     */
    public function build(ContainerInterface $container): ServerRequestInterface
    {
        $server = filter_input_array(INPUT_SERVER) ?? null;
        $payload = null;
        $method = $server['HTTP_METHOD'] ?? 'GET';
        if (isset($server['HTTP_CONTENT_TYPE']) && in_array($method, ['POST', 'PUT', 'PATCH'], true)) {
            $payload = filter_input_array(INPUT_POST);
            if ($server['HTTP_CONTENT_TYPE'] === 'application/json') {
                $payload = json_decode(file_get_contents('php://input'), true);
            }
        }

        return \Zend\Diactoros\ServerRequestFactory::fromGlobals(
            $server,
            filter_input_array(INPUT_GET) ?? null,
            $payload,
            filter_input_array(INPUT_COOKIE) ?? null
        );
    }
}
