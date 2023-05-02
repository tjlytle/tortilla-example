<?php
namespace Example\Request;

use PhoneBurner\Http\Message\ServerRequestWrapper;
use Psr\Http\Message\ServerRequestInterface;

class ServerRequest implements ServerRequestInterface
{
    use ServerRequestWrapper;

    public static function instance(ServerRequestInterface $request): self
    {
        if (!($request instanceof self)) {
            $request = new self($request);
        }

        return $request;
    }

    private function __construct(ServerRequestInterface $request)
    {
        $this->setWrapped($request);

        $this->setFactory([self::class, 'instance']);
    }

    public function getQueryParam(string $name, string $default = null): ?string
    {
        return $this->getQueryParams()[$name] ?? $default;
    }
}