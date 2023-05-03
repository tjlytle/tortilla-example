<?php

namespace Example\Api;

use Laminas\Diactoros\Response\JsonResponse;
use PhoneBurner\Http\Message\ResponseWrapper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HalResponse implements ResponseInterface
{
    use ResponseWrapper;

    private ?ResponseInterface $realized = null;

    public static function make(object $resource, Transformer $transformer, ServerRequestInterface $request, int $status = null): self
    {
        return new self($resource, $transformer, $request, $status);
    }

    private function __construct(
        public readonly object $resource,
        public readonly Transformer $transformer,
        public readonly ServerRequestInterface $request,
        private readonly ?int $status = null
    ){}

    private function getWrapped(): ResponseInterface
    {
        return $this->realized ??= new JsonResponse($this->transformer->transform($this->resource, $this->request), $status ?? 200, ['Content-Type' => 'application/hal+json']);
    }
}