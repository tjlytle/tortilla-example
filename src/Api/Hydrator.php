<?php

namespace Example\Api;

use Psr\Http\Message\ServerRequestInterface;

interface Hydrator
{
    public function update(object $resource, ServerRequestInterface $request): object;
    public function create(ServerRequestInterface $request): ?object;
    public function delete(object $resource, ServerRequestInterface $request): ?object;
}