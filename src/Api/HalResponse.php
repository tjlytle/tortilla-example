<?php

namespace Example\Api;

use Laminas\Diactoros\Response\JsonResponse;
use PhoneBurner\Http\Message\ResponseWrapper;
use Psr\Http\Message\ResponseInterface;

class HalResponse implements ResponseInterface
{
    use ResponseWrapper;

    public static function make(array $hal, int $status = null): self
    {
        return new self($hal, $status);
    }

    private function __construct(array $hal, int $status = null)
    {
        $this->setWrapped(new JsonResponse($hal, $status ?? 200, ['Content-Type' => 'application/hal+json']));
    }
}