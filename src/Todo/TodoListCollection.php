<?php

namespace Example\Todo;

use Exception;
use Traversable;

readonly class TodoListCollection implements \IteratorAggregate
{
    /**
     * @var TodoList[]
     */
    public array $lists;

    public function __construct(TodoList ...$lists)
    {
        $this->lists = $lists;
    }

    public function getIterator(): Traversable
    {
        yield from $this->lists;
    }
}