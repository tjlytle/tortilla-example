<?php

namespace Example\Api;

use Psr\Http\Message\ServerRequestInterface;

interface Transformer
{
    public function transform(object $resource, ServerRequestInterface $request): array;
}