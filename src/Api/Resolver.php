<?php

namespace Example\Api;

use Psr\Http\Message\ServerRequestInterface;

interface Resolver
{
    public function resolve(ServerRequestInterface $request): object;
}