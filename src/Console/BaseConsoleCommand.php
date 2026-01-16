<?php

namespace App\Console;

use Symfony\Component\Console\Command\Command;

abstract class BaseConsoleCommand extends Command
{
    protected const string VENDING_MACHINE_ID = 'db4463a3-6e45-4bd6-ba32-b2b9cc27c7ac';
    protected const string PAYMENT_SESSION_ID = '67ba9d4a-7890-4be2-b3f5-5cad25c66577';
}
