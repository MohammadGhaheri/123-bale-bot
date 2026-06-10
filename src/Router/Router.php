<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Router;

use OneTwoThree\BaleBot\Client\BaleClient;
use OneTwoThree\BaleBot\Contracts\BotHandlerInterface;
use OneTwoThree\BaleBot\Contracts\MiddlewareInterface;
use OneTwoThree\BaleBot\Support\Update;

final class Router
{
    /** @var array<string,BotHandlerInterface> */
    private array $commands = [];

    /** @var array<string,BotHandlerInterface> */
    private array $callbacks = [];

    /** @var list<MiddlewareInterface> */
    private array $middleware = [];

    private ?BotHandlerInterface $messageHandler = null;

    private ?BotHandlerInterface $fallbackHandler = null;

    public function command(string $command, BotHandlerInterface $handler): self
    {
        $this->commands[strtolower(ltrim($command, '/'))] = $handler;
        return $this;
    }

    public function callback(string $prefix, BotHandlerInterface $handler): self
    {
        $this->callbacks[$prefix] = $handler;
        return $this;
    }

    public function message(BotHandlerInterface $handler): self
    {
        $this->messageHandler = $handler;
        return $this;
    }

    public function fallback(BotHandlerInterface $handler): self
    {
        $this->fallbackHandler = $handler;
        return $this;
    }

    public function middleware(MiddlewareInterface $middleware): self
    {
        $this->middleware[] = $middleware;
        return $this;
    }

    public function dispatch(Update $update, BaleClient $client): void
    {
        $handler = $this->resolve($update);
        if ($handler === null) {
            return;
        }

        $pipeline = array_reduce(
            array_reverse($this->middleware),
            fn (callable $next, MiddlewareInterface $middleware): callable => fn (Update $update) => $middleware->process($update, $next),
            fn (Update $update) => $handler->handle($update, $client),
        );

        $pipeline($update);
    }

    public function resolve(Update $update): ?BotHandlerInterface
    {
        $command = $update->command();
        if ($command !== null && isset($this->commands[$command])) {
            return $this->commands[$command];
        }

        $callbackData = $update->callbackData();
        if ($callbackData !== null) {
            foreach ($this->callbacks as $prefix => $handler) {
                if (str_starts_with($callbackData, $prefix)) {
                    return $handler;
                }
            }
        }

        if ($update->message() !== null && $this->messageHandler !== null) {
            return $this->messageHandler;
        }

        return $this->fallbackHandler;
    }
}
