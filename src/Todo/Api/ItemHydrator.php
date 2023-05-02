<?php

namespace Example\Todo\Api;

use Example\Api\Hydrator;
use Example\Repository\JsonDb;
use Example\Request\ServerRequest;
use Example\Todo\Item;
use Example\Todo\Status;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;

readonly class ItemHydrator implements Hydrator
{
    public function __construct(private JsonDb $json_db){ }

    public function update(object $resource, ServerRequestInterface $request): object
    {
        $request = ServerRequest::instance($request);

        $user_id = $request->getUserId();
        $args = $request->getRouteArgs();

        if (!$args['item_id']) {
            throw new \RuntimeException('missing list context');
        }

        $item_id = Uuid::fromString($args['item_id']);

        $data = $request->getParsedBody();
        if (!isset($data['status'])) {
            throw new \RuntimeException('missing status');
        }

        $this->json_db->updateItemStatus($user_id, $item_id, Status::from(strtoupper($data['status'])));
        return $this->json_db->getItemById($user_id, $item_id);
    }

    public function create(ServerRequestInterface $request): ?object
    {
        $request = ServerRequest::instance($request);

        $item = $request->getParsedBody();

        if (!isset($item['title'])) {
            throw new \RuntimeException('missing title');
        }

        $item = Item::make($item['title'], Status::Todo);

        if (!($user_id = $request->getAttribute('user_id'))) {
            throw new \RuntimeException('no authed user');
        }

        $args = $request->getRouteArgs();

        if (!$args['list_id']) {
            throw new \RuntimeException('missing list context');
        }

        $user_id = Uuid::fromString($user_id);
        $list_id = Uuid::fromString($args['list_id']);

        $this->json_db->addItem($user_id, $list_id, $item);
        return $item;
    }

    public function delete(object $resource, ServerRequestInterface $request): ?object
    {
        throw new \RuntimeException('not implemented');
    }
}