<?php

namespace ShooglyPeg\Builder;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use ShooglyPeg\Builder\Parser;
use ShooglyPeg\Name;
use ShooglyPeg\Teams\Exceptions\InvalidTeamName;

final class Generator
{
    private const NAMESPACE = 'ShooglyPeg\Teams';
    private const CLASSNAME = 'TeamName';
    private const METHODS   = [
        [
            'name'       => 'short',
            'visibility' => 'public',
            'static'     => false,
            'body'       => 'return array_flip(self::SHORT)[$this->value] ?? $this->value;',
            'return'     => 'string',
        ],
        [
            'name'       => 'random',
            'visibility' => 'public',
            'static'     => true,
            'body'       => 'return new TeamName(self::VALID[array_rand(self::VALID)]);',
            'return'     => 'ShooglyPeg\Teams\TeamName',
        ],
        [
            'name'       => 'league',
            'visibility' => 'public',
            'static'     => true,
            'body'       => 'return array_map(fn (string $name) => new TeamName($name), array_diff(self::VALID, self::NONLEAGUE));',
            'return'     => 'array',
        ],
        [
            'name'       => 'all',
            'visibility' => 'public',
            'static'     => true,
            'body'       => 'return array_map(fn (string $name) => new TeamName($name), self::VALID);',
            'return'     => 'array',
        ],
    ];

    private array $teams;
    private string $version;

    public function __construct(
        private Parser $parser,
        private Printer $printer
    ) {
        [$teams, $version] = $this->parser->parse();

        $this->teams   = $teams;
        $this->version = $version;
    }

    public function generate(): string
    {
        $file = new PhpFile();
        $this->addFileComment($file);

        $namespace = new PhpNamespace(self::NAMESPACE);
        $namespace->addUse(InvalidTeamName::class);
        $namespace->addUse(Name::class);

        $class = $namespace->addClass(self::CLASSNAME)->setExtends(Name::class)->setFinal();
        $this->addConstants($class);
        $this->addConstructor($class);
        $this->addMethods($class);
        $file->addNamespace($namespace);

        return $this->printer->printFile($file);
    }

    private function addFileComment(PhpFile $file): void
    {
        $file->addComment('This class was autogenerated');
        $file->addComment('Do NOT edit');
        $file->addComment("Version: {$this->version}");
    }

    private function addConstructor(ClassType $class): void
    {
        $constructor = $class->addMethod('__construct');
        $constructor->addParameter('name')->setType('string');
        $constructor->setBody("if (!in_array(\$name, self::VALID)) {\n\t\$alt = self::TYPOS[\$name] ?? self::SHORT[\$name] ?? null;\n\tif (!\$alt) {\n\t\tthrow InvalidTeamName::fromValue(\$name);\n\t} else {\n\t\t\$name = \$alt;\n\t}\n}\n\n\$this->value = \$name;");
    }

    private function addConstants(ClassType $class): void
    {
        $this->addCanonicalNames($class);
        $this->addValid($class);
        $this->addNonLeague($class);
        $this->addShort($class);
        $this->addTypos($class);
    }

    private function addMethods(ClassType $class): void
    {
        foreach (self::METHODS as $m) {
            $method = $class->addMethod($m['name']);
            if ($m['visibility'] === 'private') {
                $method->setPrivate();
            }

            if ($m['static']) {
                $method->setStatic();
            }

            $method->setBody($m['body'])->setReturnType($m['return']);
        }
    }

    private function addCanonicalNames(ClassType $class): void
    {
        foreach ($this->teams as $team) {
            $class->addConstant($team['const'], $team['name']);
        }
    }

    private function addValid(ClassType $class): void
    {
        $class->addConstant(
            'VALID',
            array_map(
                fn (array $team) => $this->getSelfConstLiteral($team),
                $this->teams
            )
        )->setPrivate();
    }

    private function addNonLeague(ClassType $class): void
    {
        $class->addConstant(
            'NONLEAGUE',
            array_values(
                array_map(
                    fn (array $team) => $this->getSelfConstLiteral($team),
                    array_filter(
                        $this->teams,
                        fn (array $team) => $team['nonleague']
                    )
                )
            )
        )->setPrivate();
    }

    private function addTypos(ClassType $class): void
    {
        $class->addConstant(
            'TYPOS',
            array_reduce(
                $this->getTeamsWithTypos(),
                function (array $typos, array $team) {
                    foreach ($team['typos'] as $typo) {
                        $typos[$typo] = $this->getSelfConstLiteral($team);
                    }

                    return $typos;
                },
                []
            )
        );
    }

    private function addShort(ClassType $class): void
    {
        $class->addConstant(
            'SHORT',
            array_reduce(
                $this->getTeamsWithShortNames(),
                function (array $short, array $team) {
                    $short[$team['short']] = $this->getSelfConstLiteral($team);

                    return $short;
                },
                []
            )
        )->setPrivate();
    }

    private function getTeamsWithShortNames(): array
    {
        return $this->getTeamsWith('short');
    }

    private function getTeamsWithTypos(): array
    {
        return $this->getTeamsWith('typos');
    }

    private function getTeamsWith(string $property): array
    {
        return array_values(
            array_filter(
                $this->teams,
                fn (array $team) => !is_null($team[$property])
            )
        );
    }

    private function getSelfConstLiteral(array $team): Literal
    {
        return new Literal("self::{$team['const']}");
    }
}
