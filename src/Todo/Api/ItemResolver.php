<?php

namespace Example\Todo\Api;

use Example\Api\Resolver;
use Example\Repository\JsonDb;
use Example\Request\ServerRequest;
use Example\Todo\Item;
use Example\Todo\ItemCollection;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;

readonly class ItemResolver implements Resolver
{
    public function __construct(private JsonDb $json_db) {}

    public function resolve(ServerRequestInterface $request): Item|ItemCollection
    {
        $request = ServerRequest::instance($request);

        // the authed user
        if ($user_id = $request->getAttribute('user_id')) {
            $user_id = Uuid::fromString($user_id);
        }

        $args = $request->getAttribute('route_args') ?? [];
        // a specific id
        if (isset($args['item_id'])) {
            return $this->json_db->getItemById(
                $user_id,
                Uuid::fromString($args['item_id'])
            );
        }

        // all items for a list with optional filter
        $status = $request->getQueryParam('status', null);
        if (isset($args['list_id'])) {
            $list_id = Uuid::fromString($args['list_id']);

            $items = $this->json_db->getItemsByList($user_id, $list_id);

            if ($status) {
                $items = array_filter([...$items], fn(Item $item) => $item->status->value === strtoupper($status));
            }

            // TODO: make collection lazy so we can page it
            return new ItemCollection(...$items);
        }

        throw new \RuntimeException('missing list or item');
    }
}