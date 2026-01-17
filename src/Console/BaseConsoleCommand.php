<?php

namespace App\Console;

use Symfony\Component\Console\Command\Command;

abstract class BaseConsoleCommand extends Command
{
    protected const string VENDING_MACHINE_ID = 'db4463a3-6e45-4bd6-ba32-b2b9cc27c7ac';
    protected const string PAYMENT_SESSION_ID = '67ba9d4a-7890-4be2-b3f5-5cad25c66577';

    protected function storeCreatedPaymentSessionIdInMemory(string $uuid): void
    {
        $uuidFile = __DIR__ . '/../../var/payment_session_uuid.txt';
        file_put_contents($uuidFile, $uuid);
    }

    protected function retrievePaymentSessionIdFromMemory(): ?string
    {
        $uuidFile = __DIR__ . '/../../var/payment_session_uuid.txt';

        if (!file_exists($uuidFile)) {
            return null;
        }

        return file_get_contents($uuidFile);
    }

    protected function removePaymentSessionIdFromMemory(): void
    {
        $uuidFile = __DIR__ . '/../../var/payment_session_uuid.txt';
        unlink($uuidFile);
    }
}
