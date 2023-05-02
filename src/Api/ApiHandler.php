<?php

namespace Example\Api;

use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ApiHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly Resolver $resolver,
        private readonly Transformer $transformer,
        private readonly Hydrator $hydrator,
        private readonly Operation $operation
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return match ($this->operation) {
            Operation::READ =>  HalResponse::make(
                $this->transformer->transform(
                    $this->resolver->resolve($request),
                    $request
                )
            ),
            Operation::CREATE => (function() use ($request) {
                $response = $this->hydrator->create($request);
                if (!$response) {
                    return new EmptyResponse();
                }

                return HalResponse::make(
                    $this->transformer->transform(
                        $response,
                        $request
                    )
                );
            })(),
            Operation::UPDATE => HalResponse::make(
                $this->transformer->transform(
                    $this->hydrator->update(
                        $this->resolver->resolve($request),
                        $request),
                    $request)
            ),
            Operation::DELETE => (function() use ($request) {
                $response = $this->hydrator->delete(
                    $this->resolver->resolve($request),
                    $request
                );

                if (!$response) {
                    return new EmptyResponse();
                }

                return HalResponse::make(
                    $this->transformer->transform(
                        $response,
                        $request
                    )
                );
            })(),
        };
    }

}