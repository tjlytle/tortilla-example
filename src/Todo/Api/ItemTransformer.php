<?php

namespace Example\Todo\Api;

use Example\Api\Transformer;
use Example\Todo\Item;
use Example\Todo\ItemCollection;
use Psr\Http\Message\ServerRequestInterface;

readonly class ItemTransformer implements Transformer
{
    public function transform(object $resource, ServerRequestInterface $request): array
    {
        if ($resource instanceof Item) {
            return [
                '_links' => [
                    'self' => [
                        'href' => '/item/' . $resource->id
                    ]
                ],
                'title' => $resource->title,
                'statue' => $resource->status,
            ];
        }

        if ($resource instanceof ItemCollection) {
            return [
                '_links' => [
                    'self' => [
                        // TODO: support paging
                        'href' => '/item/'
                    ],
                    'item' => array_map(fn(Item $item) => ['href' => '/item/' . $item->id, 'title' => $item->title], iterator_to_array($resource))
                ]
            ];
        }

        throw new \RuntimeException('can not transform object: ' . get_class($resource));
    }
}