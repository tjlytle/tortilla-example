<?php

namespace Example\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;

class BearerAuth implements MiddlewareInterface
{
    public function __construct(private readonly array $keys = []) { }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $request->getHeaderLine('Authorization');

        if (0 !== strpos($token, 'Bearer:')) {
            return $handler->handle($request);
        }

        $token = trim(substr($token, 7));

        if (isset($this->keys[$token])) {
            $request = $request->withAttribute('user_id', Uuid::fromString($this->keys[$token]));
        }

        return $handler->handle($request);
    }
}