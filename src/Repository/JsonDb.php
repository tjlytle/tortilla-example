<?php

namespace Example\Repository;

use Example\Exception\NotFound;
use Example\Todo\Item;
use Example\Todo\Status;
use Example\Todo\TodoList;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class JsonDb
{
    private array $data;

    public function __construct(protected string $file)
    {
        $this->data = json_decode(file_get_contents($file), true);
    }

    /**
     * @return \Generator<TodoList>
     */
    public function getListsByUser(UuidInterface $user_id): \Generator
    {
        if (!isset($this->data[$user_id->toString()])) {
            throw new \RuntimeException('could not find user');
        }

        foreach ($this->data[$user_id->toString()]['lists'] as $id => $data) {
            yield $this->makeValue(
                TodoList::class,
                $id,
                $data['title']
            );
        }

        yield from [];
    }

    public function getListById(UuidInterface $user_id, UuidInterface $list_id): TodoList
    {
        if (!isset($this->data[$user_id->toString()])) {
            throw new \RuntimeException('could not find user');
        }

        if (!isset($this->data[$user_id->toString()]['lists'][$list_id->toString()])) {
            throw NotFound::make('list', $list_id->toString());
        }

        $data = $this->data[$user_id->toString()]['lists'][$list_id->toString()];

        return $this->makeValue(
            TodoList::class,
            $list_id->toString(),
            $data['title']
        );
    }

    public function getItemsByList(UuidInterface $user_id, UuidInterface $list_id): \Generator
    {
        if (!isset($this->data[$user_id->toString()])) {
            throw new \RuntimeException('could not find user');
        }

        if (!isset($this->data[$user_id->toString()]['lists'][$list_id->toString()])) {
            throw NotFound::make('list', $list_id->toString());
        }

        foreach ($this->data[$user_id->toString()]['lists'][$list_id->toString()]['items'] as $id => $data) {
            yield $this->makeValue(
                Item::class,
                $id,
                $data['title'],
                $data['status']
            );
        }

        yield from [];
    }

    public function getItemById(UuidInterface $user_id, UuidInterface $item_id): Item
    {
        if (!isset($this->data[$user_id->toString()])) {
            throw new \RuntimeException('could not find user');
        }

        // find the list that has our item
        foreach ($this->data[$user_id->toString()]['lists'] as $list) {
            if (array_key_exists($item_id->toString(), $list['items'])) {
                return $this->makeValue(
                    Item::class,
                    $item_id->toString(),
                    $list['items'][$item_id->toString()]['title'],
                    $list['items'][$item_id->toString()]['status'],
                );
            }
        }

        throw NotFound::make('item', $item_id->toString());
    }

    public function updateItemStatus(UuidInterface $user_id, UuidInterface $item_id, Status $status): void
    {
        if (!isset($this->data[$user_id->toString()])) {
            throw new \RuntimeException('could not find user');
        }

        foreach ($this->data[$user_id->toString()]['lists'] as $list_id => $list) {
            foreach ($list['items'] as $id => $item) {
                if ($id === $item_id->toString()) {
                    $this->data[$user_id->toString()]['lists'][$list_id]['items'][$id]['status'] = $status->name;
                    $this->flush();
                    return;
                }
            }
        }

        throw NotFound::make('item', $item_id->toString());
    }

    public function addItem(UuidInterface $user_id, UuidInterface $list_id, Item $item): void
    {
        if (!isset($this->data[$user_id->toString()])) {
            throw new \RuntimeException('could not find user');
        }

        if (!isset($this->data[$user_id->toString()]['lists'][$list_id->toString()])) {
            throw NotFound::make('list', $list_id->toString());
        }

        $this->data[$user_id->toString()]['lists'][$list_id->toString()]['items'][$item->id->toString()] = [
            'status' => $item->status->name,
            'title' => $item->title,
        ];

        $this->flush();
    }

    public function addList(UuidInterface $user_id, TodoList $list): void
    {
        $this->data[$user_id->toString()]['lists'][$list->id->toString()] = [
            'title' => $list->title,
            'items' => [],
        ];

        $this->flush();
    }

    private function flush(): void
    {
        file_put_contents($this->file, json_encode($this->data, JSON_PRETTY_PRINT));
    }

    private function makeValue(string $class, string $id, mixed ...$data): object
    {
        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();
        $constructor->setAccessible(true);
        $value = $reflection->newInstanceWithoutConstructor();
        $constructor->invoke($value, Uuid::fromString($id), ...$data);
        return $value;
    }
}