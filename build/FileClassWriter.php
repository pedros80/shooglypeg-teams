<?php

namespace ShooglyPeg\Builder;

use League\Flysystem\Filesystem;
use ShooglyPeg\Builder\ClassWriter;

final class FileClassWriter implements ClassWriter
{
    public function __construct(
        private Filesystem $fileSystem
    ) {
    }

    public function write(string $content): void
    {
        $this->fileSystem->write('TeamName.php', $content);
    }
}
