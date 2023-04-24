<?php

namespace Example\Todo;

use Exception;
use Traversable;

readonly class ItemCollection implements \IteratorAggregate
{
    public array $items;
    public function __construct(Item ...$items)
    {
        $this->items = $items;
    }

    public function getIterator(): Traversable
    {
        yield from $this->items;
    }
}