<?php

namespace Example\Todo;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

readonly class Item
{
    public Status $status;
    private function __construct(public UuidInterface $id, public string $title, string|Status $status) {
        if (!($status instanceof Status)) {
            $status = Status::from(strtoupper($status));
        }

        $this->status = $status;
    }

    public static function make(string $title, string|Status $status): self
    {
        return new self(Uuid::uuid4(), $title, $status);
    }
}