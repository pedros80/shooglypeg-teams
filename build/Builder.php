<?php

namespace ShooglyPeg\Builder;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

require __DIR__ . '/../vendor/autoload.php';

final class Builder
{
    public function __construct(
        private Generator $generator,
        private ClassWriter $classWriter
    ) {
    }

    public function build(): void
    {
        $this->classWriter->write($this->generator->generate());
    }
}

if (isset($argv[1]) && $argv[1] === 'dry-run') {
    $writer = new ConsoleClassWriter();
} else {
    $writer =
        new FileClassWriter(
            new Filesystem(
                new LocalFilesystemAdapter('src')
            )
        );
}

$builder = new Builder(
    new Generator(
        new Parser(
            new Filesystem(new LocalFilesystemAdapter('data'))
        ),
        new Printer()
    ),
    $writer
);

$builder->build();
