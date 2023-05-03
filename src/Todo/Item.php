<?php

namespace Example\Todo;

use Laminas\Diactoros\Uri;
use PhoneBurner\Http\Message\UriWrapper;
use Psr\Http\Message\UriInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Item implements UriInterface
{
    use UriWrapper;

    public readonly Status $status;
    private function __construct(public readonly UuidInterface $id, public readonly string $title, string|Status $status) {
        if (!($status instanceof Status)) {
            $status = Status::from(strtoupper($status));
        }

        $this->status = $status;
        $this->setWrapped(new Uri('/item/' . $this->id->toString()));
    }

    public static function make(string $title, string|Status $status): self
    {
        return new self(Uuid::uuid4(), $title, $status);
    }
}