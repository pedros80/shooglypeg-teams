<?php

namespace ShooglyPeg\Builder;

interface ClassWriter
{
    public function write(string $content): void;
}
