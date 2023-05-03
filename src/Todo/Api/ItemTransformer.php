<?php

namespace Example\Todo\Api;

use Example\Api\Transformer;
use Example\Request\ServerRequest;
use Example\Todo\Item;
use Example\Todo\ItemCollection;
use Psr\Http\Message\ServerRequestInterface;

readonly class ItemTransformer implements Transformer
{
    public function transform(object $resource, ServerRequestInterface $request): array
    {
        $request = ServerRequest::instance($request);

        if ($resource instanceof Item) {
            return [
                '_links' => [
                    'self' => [
                        'href' => (string) $resource
                    ]
                ],
                'title' => $resource->title,
                'status' => $resource->status,
            ];
        }

        // we only get this when using a list
        $args = $request->getRouteArgs();
        if (empty($args['list_id'])) {
            throw new \RuntimeException('can not transform item collection without list context');
        }

        if ($resource instanceof ItemCollection) {
            return [
                '_links' => [
                    'self' => [
                        // TODO: support paging
                        'href' => '/list/' . $args['list_id'] . '/items'
                    ],
                    'item' => array_map(fn(Item $item) => ['href' => (string) $item, 'title' => $item->title], iterator_to_array($resource))
                ]
            ];
        }

        throw new \RuntimeException('can not transform object: ' . get_class($resource));
    }
}