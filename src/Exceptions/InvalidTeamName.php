<?php

declare(strict_types=1);

namespace ShooglyPeg\Teams\Exceptions;

use Exception;

final class InvalidTeamName extends Exception
{
    private function __construct(string $message)
    {
        parent::__construct($message, 400);
    }

    public static function fromValue(string $name): InvalidTeamName
    {
        return new InvalidTeamName("{$name} is not a valid Team Name.");
    }
}
