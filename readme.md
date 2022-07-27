# ShooglyPeg - Teams

![Code Coverage Badge](./coverage_badge.svg)

Class to model team names with code generation from a csv of

- canonical name
- optional short name
- list of potential typos and different spellings
- is this team non-league?

```php

// these three are equivalent
$name  = new TeamName('Airdrieonians'); // Cannical name
$short = new TeamName('Airdrie'); // Short name
$typo  = new TeamName('Airdrie United'); // Out of date but we might get from an external source

$name->short(); // return Airdrie

$name = TeamName::random(); // a random team

$league = TeamName::league(); // an array of all current league teams

$all = TeamName::all(); // an array of all current league teams plus any non-league teams recently in the league
```

Class is generated from a csv like the following.

```
"Aberdeen",
"Airdrieonians","Airdrie","Airdrie United"
"Albion Rovers","Albion"
"Alloa Athletic","Alloa"
"Annan Athletic","Annan
"Arbroath"
"Ayr United","Ayr","Ayr Utd"
"Bonnyrigg Rose Athletic","Bonnyrigg"
"Berwick Rangers","Berwick","NONLEAGUE"
```
