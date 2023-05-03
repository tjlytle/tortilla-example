<?php

namespace Example\Middleware;

use Example\Api\HalResponse;
use Example\Request\ServerRequest;
use Example\Todo\ItemCollection;
use Example\Todo\TodoListCollection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Pager implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        if (!($response instanceof HalResponse)) {
            return $response;
        }

        $request = ServerRequest::instance($request);

        $size = (int) $request->getQueryParam('size');
        $page = (int) $request->getQueryParam('page');

        if (!$size || !$page) {
            return $response;
        }

        $page--; //the first page is really the zeroth

        if ($response->resource instanceof ItemCollection) {
            return HalResponse::make(
                new ItemCollection(...$this->page($response->resource, $size, $page)),
                $response->transformer,
                $response->request
            );
        }

        if ($response->resource instanceof TodoListCollection) {
            return HalResponse::make(
                new TodoListCollection(...$this->page($response->resource, $size, $page)),
                $response->transformer,
                $response->request
            );
        }

        return $response;
    }

    private function page(\Traversable $resource, int $size, int $page): array
    {
        return array_slice(iterator_to_array($resource), $size * $page, $size);
    }
}