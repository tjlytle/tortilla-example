<?php

use Example\Api\Operation;
use Example\Middleware\ApiDispatcher;
use Example\Middleware\BearerAuth;
use Example\Middleware\Pager;
use Example\Middleware\ExceptionHandler;
use Example\Middleware\NotFoundHandler;
use Example\Middleware\RequireUser;
use Example\Middleware\RouteMatch;
use Example\Middleware\StackHandler;
use Example\Repository\JsonDb;
use Example\Route\Config;
use Example\Todo\Api\ItemHydrator;
use Example\Todo\Api\ItemResolver;
use Example\Todo\Api\ItemTransformer;
use Example\Todo\Api\ListHydrator;
use Example\Todo\Api\ListResolver;
use Example\Todo\Api\ListTransformer;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;

$autoload = require_once __DIR__ . '/../vendor/autoload.php';

// our 'container'
$repository = new JsonDb(__DIR__ . '/../db/db.json');
$list_resolver = new ListResolver($repository);
$list_hydrator = new ListHydrator($repository);
$item_resolver = new ItemResolver($repository);
$item_hydrator = new ItemHydrator($repository);
$item_transformer = new ItemTransformer();
$list_transformer = new ListTransformer();

// our routes
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) use ($list_resolver, $item_resolver, $list_hydrator, $item_hydrator, $list_transformer, $item_transformer) {
    // get a collection of lists or create a new list
    $r->addRoute('GET', '/list[/]', new Config(
        $list_resolver,
        $list_transformer,
        $list_hydrator,
        Operation::READ
    ));

    $r->addRoute('POST', '/list[/]', new Config(
        $list_resolver,
        $list_transformer,
        $list_hydrator,
        Operation::CREATE
    ));

    // get a specific list
    $r->addRoute('GET', '/list/{list_id}[/]', new Config(
        $list_resolver,
        $list_transformer,
        $list_hydrator,
        Operation::READ
    ));

    // get a collection of items or create a new item
    $r->addRoute('GET', '/list/{list_id}/items', new Config(
        $item_resolver,
        $item_transformer,
        $item_hydrator,
        Operation::READ
    ));

    $r->addRoute('POST', '/list/{list_id}/items', new Config(
        $list_resolver,
        $item_transformer,
        $item_hydrator,
        Operation::CREATE
    ));

    // get an item or update the state of an item
    $r->addRoute('GET', '/item/{item_id}', new Config(
        $item_resolver,
        $item_transformer,
        $item_hydrator,
        Operation::READ
    ));

    $r->addRoute(['POST', 'PUT'], '/item/{item_id}', new Config(
        $item_resolver,
        $item_transformer,
        $item_hydrator,
        Operation::UPDATE
    ));
});

// config middleware stack, default handler is not found
$stack = new StackHandler(new NotFoundHandler());
$stack->add(new ExceptionHandler());
$stack->add(new BearerAuth([
    'super_secret' => 'fba7928f-d214-4ea7-9b4c-e5c24e350db5'
]));
$stack->add(new RequireUser());
$stack->add(new RouteMatch($dispatcher));
$stack->add(new Pager());
$stack->add(new ApiDispatcher());

// app execution
$request = ServerRequestFactory::fromGlobals();
$response = $stack->handle($request);
$emitter = new SapiEmitter();
$emitter->emit($response);