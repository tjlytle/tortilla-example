<?php

namespace Example\Middleware;

use Example\Request\ServerRequest;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequireUser implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $request = ServerRequest::instance($request);
        if (!$request->hasUserId()) {
            return new TextResponse('401 Unauthorized', 401);
        }

        return $handler->handle($request);
    }
}