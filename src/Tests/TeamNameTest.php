<?php

declare(strict_types=1);

namespace ShooglyPeg\Teams\Tests;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use PHPUnit\Framework\TestCase;
use ShooglyPeg\Teams\Exceptions\InvalidTeamName;
use ShooglyPeg\Teams\TeamName;

final class TeamNameTest extends TestCase
{
    public function testInstantiates(): void
    {
        $name = new TeamName(TeamName::QUEENS_PARK);

        $this->assertInstanceOf(TeamName::class, $name);

        $this->assertEquals('queen-s--park', $name->slug());
        $this->assertEquals("Queen's Park", TeamName::fromSlug($name->slug()));
        $this->assertEquals("Queen's Pk", $name->short());

        $from_typo  = new TeamName('Queens Park');
        $from_short = new TeamName("Queen's Pk");

        $this->assertTrue($name->equals($from_typo));
        $this->assertTrue($name->equals($from_short));
        $this->assertFalse($name->equals(new TeamName(TeamName::QUEEN_OF_THE_SOUTH)));
    }

    public function testAll(): void
    {
        $all = TeamName::all();

        $filesystem = new Filesystem(new LocalFilesystemAdapter('data'));
        $source     = explode("\n", $filesystem->read('names.csv'));

        $this->assertCount(count($source), $all);
    }

    public function testRandom(): void
    {
        $name = TeamName::random();

        $this->assertInstanceOf(TeamName::class, $name);
    }

    public function testLeague(): void
    {
        $league = TeamName::league();

        $filesystem = new Filesystem(new LocalFilesystemAdapter('data'));
        $source     = array_values(
            array_filter(
                explode("\n", $filesystem->read('names.csv')),
                fn (string $row) => !str_contains($row, 'NONLEAGUE')
            )
        );

        $this->assertCount(count($source), $league);
    }

    public function testInvalid(): void
    {
        $this->expectException(InvalidTeamName::class);
        $this->expectExceptionMessage('Invalid FC is not a valid Team Name.');

        new TeamName('Invalid FC');
    }
}
