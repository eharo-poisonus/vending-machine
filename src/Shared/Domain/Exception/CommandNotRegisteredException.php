<?php

namespace App\Shared\Domain\Exception;

use App\Shared\Domain\Bus\Command\Command;
use RuntimeException;

final class CommandNotRegisteredException extends RuntimeException
{
    public function __construct(Command $command)
    {
        $commandClass = $command::class;
        parent::__construct("The command <$commandClass> hasn't a command handler associated");
    }
}
