<?php

namespace Example\Route;

use Example\Api\Hydrator;
use Example\Api\Operation;
use Example\Api\Resolver;
use Example\Api\Transformer;

readonly class Config
{
    public function __construct(
        public Resolver $resolver,
        public Transformer $transformer,
        public Hydrator $hydrator,
        public Operation $operation
    ) {}
}