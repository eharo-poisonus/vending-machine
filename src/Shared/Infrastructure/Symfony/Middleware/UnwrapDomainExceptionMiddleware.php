<?php

namespace App\Shared\Infrastructure\Symfony\Middleware;

use App\Shared\Domain\Exception\DomainException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class UnwrapDomainExceptionMiddleware implements MiddlewareInterface
{
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        try {
            return $stack->next()->handle($envelope, $stack);
        } catch (HandlerFailedException $e) {
            foreach ($e->getWrappedExceptions() as $exception) {
                if ($exception instanceof DomainException) {
                    throw $exception;
                }
            }

            throw $e;
        }
    }
}
