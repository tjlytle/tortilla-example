<?php

namespace Example\Todo\Api;

use Example\Api\Hydrator;
use Example\Repository\JsonDb;
use Example\Request\ServerRequest;
use Example\Todo\TodoList;
use Psr\Http\Message\ServerRequestInterface;

class ListHydrator implements Hydrator
{
    public function __construct(private JsonDb $json_db){ }

    public function update(object $resource, ServerRequestInterface $request): object
    {
        throw new \RuntimeException('not implemented');
    }

    public function create(ServerRequestInterface $request): ?object
    {
        $request = ServerRequest::instance($request);
        $user_id = $request->getUserId();

        $list = TodoList::make($request->getParsedBody()['title']);

        $this->json_db->addList($user_id, $list);
        return $list;
    }

    public function delete(object $resource, ServerRequestInterface $request): ?object
    {
        throw new \RuntimeException('not implemented');
    }

}