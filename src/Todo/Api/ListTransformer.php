<?php

namespace Example\Todo\Api;

use Example\Api\Transformer;
use Example\Todo\Item;
use Example\Todo\TodoList;
use Example\Todo\TodoListCollection;
use Psr\Http\Message\ServerRequestInterface;

readonly class ListTransformer implements Transformer
{
    public function transform(object $resource, ServerRequestInterface $request): array
    {
        if ($resource instanceof TodoList) {
            return [
                '_links' => [
                    'self' => [
                        'href' => '/list/' . $resource->id
                    ]
                ],
                'title' => $resource->title,
            ];
        }

        if ($resource instanceof TodoListCollection) {
            return [
                '_links' => [
                    'self' => [
                        // TODO: support paging
                        'href' => '/list/'
                    ],
                    'list' => array_map(fn(TodoList $item) => ['href' => '/list/' . $item->id, 'title' => $item->title], iterator_to_array($resource))
                ]
            ];
        }

        throw new \RuntimeException('can not transform object: ' . get_class($resource));
    }
}