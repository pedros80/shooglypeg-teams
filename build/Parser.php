<?php

namespace ShooglyPeg\Builder;

use League\Flysystem\Filesystem;

final class Parser
{
    private array $data;
    private string $version;

    public function __construct(
        private Filesystem $filesystem
    ) {
        $this->loadData();
    }

    public function parse(): array
    {
        return [
            $this->data,
            $this->version,
        ];
    }

    private function loadData(): void
    {
        $this->data    = $this->parseCsv('names');
        $this->version = $this->filesystem->read('version.txt');
    }

    private function parseCsv(string $file): array
    {
        return array_reduce(
            explode("\n", $this->filesystem->read("{$file}.csv")),
            function (array $out, string $team) {
                $out[] = $this->parseTeam(str_getcsv($team));

                return $out;
            },
            []
        );
    }

    private function parseTeam(array $team): array
    {
        return [
            'const'     => $this->getConstName($team[0]),
            'name'      => $team[0],
            'short'     => $this->getShortName($team),
            'typos'     => $this->getTypos($team),
            'nonleague' => $this->getNonLeague($team),
        ];
    }

    private function getNonLeague(array $team): bool
    {
        return $team[count($team) - 1] === 'NONLEAGUE';
    }

    private function getConstName(string $name): string
    {
        return strtoupper(str_replace([' ', '\''], ['_', ''], $name));
    }

    private function getShortName(array $team): ?string
    {
        return isset($team[1]) && strlen($team[1]) > 0 && $team[1] !== 'NONLEAGUE' ? $team[1] : null;
    }

    private function getTypos(array $team): ?array
    {
        $team = array_filter(
            $team,
            fn (string $entry) => $entry !== 'NONLEAGUE'
        );
        if (count($team) > 2) {
            return array_slice($team, 2);
        }

        return null;
    }
}
