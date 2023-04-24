<?php

namespace Example\Todo;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

readonly class TodoList
{
    private function __construct(public UuidInterface $id, public string $title) {}

    public static function make(string $title): self
    {
        return new self(Uuid::uuid4(), $title);
    }
}