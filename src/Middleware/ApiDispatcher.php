<?php

namespace Example\Middleware;

use Example\Api\ApiHandler;
use Example\Request\ServerRequest;
use Example\Route\Config;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ApiDispatcher implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $request = ServerRequest::instance($request);

        if(!$config = $request->getRouteConfig()) {
            return $handler->handle($request);
        }

        $handler = new ApiHandler(
            $config->resolver,
            $config->transformer,
            $config->hydrator,
            $config->operation
        );

        return $handler->handle($request);
    }
}