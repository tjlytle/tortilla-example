<?php

namespace Example\Todo\Api;

use Example\Api\Resolver;
use Example\Repository\JsonDb;
use Example\Todo\TodoList;
use Example\Todo\TodoListCollection;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;

readonly class ListResolver implements Resolver
{
    public function __construct(private JsonDb $json_db) {}

    public function resolve(ServerRequestInterface $request): TodoList|TodoListCollection
    {
        if ($user_id = $request->getAttribute('user_id')) {
            $user_id = Uuid::fromString($user_id);
        }

        $args = $request->getAttribute('route_args') ?? [];
        // a specific list
        if (isset($args['list_id'])) {
            return $this->json_db->getListById($user_id, Uuid::fromString($args['list_id']));
        }

        return new TodoListCollection(...$this->json_db->getListsByUser($user_id));
    }
}