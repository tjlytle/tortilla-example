<?php

namespace Example\Middleware;

use Example\Route\Config;
use FastRoute\Dispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RouteMatch implements MiddlewareInterface
{
    public function __construct(private readonly Dispatcher $dispatcher) { }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $result = $this->dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

        if ($result[0] === Dispatcher::FOUND) {
            $request = $request->withAttribute(Config::class, $result[1])->withAttribute('route_args', $result[2]);
        }

        return $handler->handle($request);
    }

}