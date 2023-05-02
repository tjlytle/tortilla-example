<?php
namespace Example\Request;

use Example\Route\Config;
use PhoneBurner\Http\Message\ServerRequestWrapper;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

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

    public function getUserId(): UuidInterface
    {
        if (!$id = $this->getAttribute('user_id')) {
            throw new \RuntimeException('missing user id');
        }

        return Uuid::fromString($id);
    }

    public function hasUserId(): bool
    {
        return $this->getAttribute('user_id') !== null;
    }

    public function getRouteConfig(): ?Config
    {
        return $this->getAttribute(Config::class);
    }

    public function getRouteArgs(): array
    {
        return $this->getAttribute('route_args') ?? [];
    }
}