<?php

namespace Example\Exception;

use Laminas\Diactoros\Response\TextResponse;
use PhoneBurner\Http\Message\ResponseWrapper;
use Psr\Http\Message\ResponseInterface;

class NotFound extends \RuntimeException implements ResponseInterface
{
    use ResponseWrapper;

    public static function make(string $name, string $id = null, string $message = ""): self
    {
        $self = new self($message);
        $self->setWrapped(new TextResponse("🔎 Not Found\n$name: $id", 404));
        return $self;
    }

    private function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}