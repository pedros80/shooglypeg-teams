<?php

namespace ShooglyPeg\Builder;

use ShooglyPeg\Builder\ClassWriter;

final class ConsoleClassWriter implements ClassWriter
{
    public function write(string $content): void
    {
        echo "{$content}\n";
    }
}
