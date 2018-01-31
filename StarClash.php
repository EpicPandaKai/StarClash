<?php require_once('../initialize.php');


// Init
$TeamOne = new Player("Player 1", "Player 2", "Player 3", "Player 4");
$TeamTwo = new Player("Player 5", "Player 6", "Player 7", "Player 8");

$PT1 = new Player("Player 2");
$PT2 = new Player("Player 5");

$Players = new Player("Player 1", "Player 2", "Player 3", "Player 4", "Player 5", "Player 6", "Player 7", "Player 8");

$P1 = new Player("Player 1");
$P2 = new Player("Player 2");
$P3 = new Player("Player 3");
$P4 = new Player("Player 4");
$P5 = new Player("Player 5");
$P6 = new Player("Player 6");
$P7 = new Player("Player 7");
$P8 = new Player("Player 8");
$P = new Player("Current Player");


$messagetimer = new Deathcounter($Players,60);
$restimer = new Deathcounter($Players, 34);
$unitsTimer = new Deathcounter($Players, 60);

// Victory/Defeat
$TeamOne->_if(Bring($PT1, "Buildings", "At Most", 0, "ReplaceBottom"))->then(Defeat(), Comment("TeamOneDefeat"));
$TeamOne->_if(Bring($PT2, "Buildings", "At Most", 0, "ReplaceTop"))->then(Victory(), Comment("TeamOneVictory"));

$TeamTwo->_if(Bring($PT1, "Buildings", "At Most", 0, "ReplaceBottom"))->then(Victory(), Comment("TeamTwoVictory"));
$TeamTwo->_if(Bring($PT2, "Buildings", "At Most", 0, "ReplaceTop"))->then(Defeat(), Comment("TeamTwoDefeat"));

// Timers
$Players->justonce($messagetimer->setTo(60));
$Players->justonce($restimer->setTo(34));
$Players->justonce($unitsTimer->setTo(60));


$Players->always(
	$messagetimer->subtract(1),
	$restimer->subtract(1),
	$unitsTimer->subtract(1),
'');

// Reset Units order timer
$Players->_if($unitsTimer->exactly(0))->then($unitsTimer->setTo(120));

// Elixir 
$Players->_if( $restimer->exactly(0), Accumulate($P, "At Most", 14, "ore") )->then(
	SetResources(CP,'Add', 1, 'Ore'),
	$restimer->setTo(34),
	Comment("Elixir"),
'');



// Alliances
$TeamOne->always(SetAlly($TeamOne), SetEnemy($TeamTwo));
$TeamTwo->always(SetAlly($TeamTwo), SetEnemy($TeamOne));

// Tracking Zones
$P1->always(MoveLocation('P1u', CP, 'Protoss Observer', 'Anywhere'));
$P3->always(MoveLocation('P3u', CP, 'Protoss Observer', 'Anywhere'));
$P4->always(MoveLocation('P4u', CP, 'Protoss Observer', 'Anywhere'));
$P6->always(MoveLocation('P6u', CP, 'Protoss Observer', 'Anywhere'));
$P7->always(MoveLocation('P7u', CP, 'Protoss Observer', 'Anywhere'));
$P8->always(MoveLocation('P8u', CP, 'Protoss Observer', 'Anywhere'));

$TeamOne->_if(Bring(CP, "Protoss Observer", "At Least", 1, "MapTopLeft"), Bring($PT2, "Terran Bunker", "At Least", 1, "MapTopLeft"))
	->then(MoveUnit(CP, "Protoss Observer", 1, "MapTopLeft", "MapBottomLeft"), CenterView("MapBottomLeft"), Display('\x013Destroy the top left bunker to go there !'));
$TeamOne->_if(Bring(CP, "Protoss Observer", "At Least", 1, "MapTopRight"), Bring($PT2, "Terran Bunker", "At Least", 1, "MapTopRight"))
	->then(MoveUnit(CP, "Protoss Observer", 1, "MapTopRight", "MapBottomRight"), CenterView("MapBottomRight"), Display('\x013Destroy the top right bunker to go there !'));
$TeamOne->_if(Bring(CP, "Protoss Observer", "At Least", 1, "MapTopLeft"), Bring($P, "Protoss Observer", "At Most", 0, "MapTopLeftOutpost"))
	->then(MoveUnit(CP, "Protoss Observer", 1, "MapTopLeft", "MapTopLeftOutpost"), CenterView("MapTopLeftOutpost"), Display('\x013Cannot go that near !'));
$TeamOne->_if(Bring(CP, "Protoss Observer", "At Least", 1, "MapTopRight"), Bring($P, "Protoss Observer", "At Most", 0, "MapTopRightOutpost"))
	->then(MoveUnit(CP, "Protoss Observer", 1, "MapTopRight", "MapTopRightOutpost"), CenterView("MapTopRightOutpost"), Display('\x013Cannot go that near !'));

$TeamTwo->_if(Bring(CP, "Protoss Observer", "At Least", 1, "MapBottomLeft"), Bring($PT1, "Terran Bunker", "At Least", 1, "MapBottomLeft"))
	->then(MoveUnit(CP, "Protoss Observer", 1, "MapBottomLeft", "MapTopLeft"), CenterView("MapTopLeft"), Display('\x013Destroy the bottom left bunker to go there !'));
$TeamTwo->_if(Bring(CP, "Protoss Observer", "At Least", 1, "MapBottomRight"), Bring($PT1, "Terran Bunker", "At Least", 1, "MapBottomRight"))
	->then(MoveUnit(CP, "Protoss Observer", 1, "MapBottomRight", "MapTopRight"), CenterView("MapTopRight"), Display('\x013Destroy the bottom right bunker to go there !'));
$TeamTwo->_if(Bring(CP, "Protoss Observer", "At Least", 1, "MapBottomLeft"),Bring($P, "Protoss Observer", "At Most", 0, "MapBottomLeftOutpost"))
	->then(MoveUnit(CP, "Protoss Observer", 1, "MapBottomLeft", "MapBottomLeftOutpost"), CenterView("MapBottomLeftOutpost"), Display('\x013Cannot go that near !'));
$TeamTwo->_if(Bring(CP, "Protoss Observer", "At Least", 1, "MapBottomRight"),Bring($P, "Protoss Observer", "At Most", 0, "MapBottomRightOutpost"))
	->then(MoveUnit(CP, "Protoss Observer", 1, "MapBottomRight", "MapBottomRightOutpost"), CenterView("MapBottomRightOutpost"), Display('\x013Cannot go that near !'));

// Bunkers
$P2->justonce(RunAIScriptAtLocation("EnBk", "Tower3"));
$P2->justonce(RunAIScriptAtLocation("EnBk", "Tower4"));
$P5->justonce(RunAIScriptAtLocation("EnBk", "Tower1"));
$P5->justonce(RunAIScriptAtLocation("EnBk", "Tower2"));

// Orders
/*
$PT1->_if($unitsTimer->atMost(1))->then(Order($P, "Men", "MapBottomLeft", "Attack", "Tower1"));
$PT1->_if($unitsTimer->atMost(1))->then(Order($P, "Men", "MapBottomRight", "Attack", "Tower2"));
$PT1->_if($unitsTimer->atMost(1), Bring($PT2, "Terran Bunker", "Exactly", 0, "Tower1"))->then(Order($P, "Men", "MapTopLeft", "Attack", "ReplaceTop"));
$PT1->_if($unitsTimer->atMost(1), Bring($PT2, "Terran Bunker", "Exactly", 0, "Tower2"))->then(Order($P, "Men", "MapTopRight", "Attack", "ReplaceTop"));
*/

$PT2->_if($unitsTimer->atMost(1))->then(Order($P, "Men", "MapTopLeft", "Patrol", "Tower4"));
$PT2->_if($unitsTimer->atMost(1))->then(Order($P, "Men", "MapTopRight", "Patrol", "Tower3"));
$PT2->_if($unitsTimer->atMost(1), Bring($PT1, "Terran Bunker", "Exactly", 0, "Tower3"))->then(Order($P, "Men", "MapBottomRight", "Patrol", "ReplaceBottom"));
$PT2->_if($unitsTimer->atMost(1), Bring($PT1, "Terran Bunker", "Exactly", 0, "Tower4"))->then(Order($P, "Men", "MapBottomLeft", "Patrol", "ReplaceBottom"));


// Units !

// 1: Zergling 2
/*
$P1->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u01"), Accumulate($P, "At Least", 1, "ore"))->then(CreateUnit($PT1, "Zerg Zergling", 2, "P1u"), SetResources($P, "Subtract", 1, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u01", "T1u01b"), $unitsTimer->setTo(1));
*/
$P1->
	_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u01"), Accumulate($P, "At Least", 1, "ore"))
	->then(CreateUnit($PT1, "Zerg Zergling", 2, "P1u"), SetResources($P, "Subtract", 1, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u01", "T1u01b"),
		_if(Bring($P, "Protoss Observer", "Exactly", 1, "MapBottomLeft"))
			->then(Order($PT1, "Men", "MapBottomLeft", "Attack", "MapTopLeft")),
		_elseif(Bring($P, "Protoss Observer", "Exactly", 1, "MapBottomRight")),
			->then(Order($PT1, "Men", "MapBottomLeft", "Attack", "MapTopLeft")),
	'');


$P3->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u01"), Accumulate($P, "At Least", 1, "ore"))->then(CreateUnit($PT1, "Zerg Zergling", 2, "P3u"), SetResources($P, "Subtract", 1, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u01", "T1u01b"), $unitsTimer->setTo(1));
$P4->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u01"), Accumulate($P, "At Least", 1, "ore"))->then(CreateUnit($PT1, "Zerg Zergling", 2, "P4u"), SetResources($P, "Subtract", 1, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u01", "T1u01b"), $unitsTimer->setTo(1));


$P6->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u01"), Accumulate($P, "At Least", 1, "ore"))->then(CreateUnit($PT2, "Zerg Zergling", 2, "P6u"), SetResources($P, "Subtract", 1, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u01", "T2u01b"), $unitsTimer->setTo(1));
$P7->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u01"), Accumulate($P, "At Least", 1, "ore"))->then(CreateUnit($PT2, "Zerg Zergling", 2, "P7u"), SetResources($P, "Subtract", 1, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u01", "T2u01b"), $unitsTimer->setTo(1));
$P8->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u01"), Accumulate($P, "At Least", 1, "ore"))->then(CreateUnit($PT2, "Zerg Zergling", 2, "P8u"), SetResources($P, "Subtract", 1, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u01", "T2u01b"), $unitsTimer->setTo(1));

// 2: Marine 1 & Firebat 1
$P1->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u02"), Accumulate($P, "At Least", 2, "ore"))->then(CreateUnit($PT1, "Terran Marine", 1, "P1u"), CreateUnit($PT1, "Terran Firebat", 1, "P1u"), SetResources($P, "Subtract", 2, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u02", "T1u02b"), $unitsTimer->setTo(1));
$P3->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u02"), Accumulate($P, "At Least", 2, "ore"))->then(CreateUnit($PT1, "Terran Marine", 1, "P3u"), CreateUnit($PT1, "Terran Firebat", 1, "P3u"), SetResources($P, "Subtract", 2, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u02", "T1u02b"), $unitsTimer->setTo(1));
$P4->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u02"), Accumulate($P, "At Least", 2, "ore"))->then(CreateUnit($PT1, "Terran Marine", 1, "P4u"), CreateUnit($PT1, "Terran Firebat", 1, "P4u"), SetResources($P, "Subtract", 2, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u02", "T1u02b"), $unitsTimer->setTo(1));


$P6->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u02"), Accumulate($P, "At Least", 2, "ore"))->then(CreateUnit($PT2, "Terran Marine", 1, "P6u"), CreateUnit($PT1, "Terran Firebat", 1, "P6u"), SetResources($P, "Subtract", 2, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u02", "T2u02b"), $unitsTimer->setTo(1));
$P7->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u02"), Accumulate($P, "At Least", 2, "ore"))->then(CreateUnit($PT2, "Terran Marine", 1, "P7u"), CreateUnit($PT1, "Terran Firebat", 1, "P7u"), SetResources($P, "Subtract", 2, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u02", "T2u02b"), $unitsTimer->setTo(1));
$P8->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u02"), Accumulate($P, "At Least", 2, "ore"))->then(CreateUnit($PT2, "Terran Marine", 1, "P8u"), CreateUnit($PT1, "Terran Firebat", 1, "P8u"), SetResources($P, "Subtract", 2, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u02", "T2u02b"), $unitsTimer->setTo(1));


// 3: Zealot 2
$P1->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u03"), Accumulate($P, "At Least", 3, "ore"))->then(CreateUnit($PT1, "Protoss Zealot", 2, "P1u"), SetResources($P, "Subtract", 3, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u03", "T1u03b"), $unitsTimer->setTo(1));
$P3->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u03"), Accumulate($P, "At Least", 3, "ore"))->then(CreateUnit($PT1, "Protoss Zealot", 2, "P3u"), SetResources($P, "Subtract", 3, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u03", "T1u03b"), $unitsTimer->setTo(1));
$P4->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u03"), Accumulate($P, "At Least", 3, "ore"))->then(CreateUnit($PT1, "Protoss Zealot", 2, "P4u"), SetResources($P, "Subtract", 3, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u03", "T1u03b"), $unitsTimer->setTo(1));


$P6->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u03"), Accumulate($P, "At Least", 3, "ore"))->then(CreateUnit($PT2, "Protoss Zealot", 2, "P6u"), SetResources($P, "Subtract", 3, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u03", "T2u03b"), $unitsTimer->setTo(1));
$P7->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u03"), Accumulate($P, "At Least", 3, "ore"))->then(CreateUnit($PT2, "Protoss Zealot", 2, "P7u"), SetResources($P, "Subtract", 3, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u03", "T2u03b"), $unitsTimer->setTo(1));
$P8->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u03"), Accumulate($P, "At Least", 3, "ore"))->then(CreateUnit($PT2, "Protoss Zealot", 2, "P8u"), SetResources($P, "Subtract", 3, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u03", "T2u03b"), $unitsTimer->setTo(1));

// 4: Hydra 3
$P1->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u04"), Accumulate($P, "At Least", 4, "ore"))->then(CreateUnit($PT1, "Zerg Hydralisk", 3, "P1u"), SetResources($P, "Subtract", 4, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u04", "T1u04b"), $unitsTimer->setTo(1));
$P3->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u04"), Accumulate($P, "At Least", 4, "ore"))->then(CreateUnit($PT1, "Zerg Hydralisk", 3, "P3u"), SetResources($P, "Subtract", 4, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u04", "T1u04b"), $unitsTimer->setTo(1));
$P4->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u04"), Accumulate($P, "At Least", 4, "ore"))->then(CreateUnit($PT1, "Zerg Hydralisk", 3, "P4u"), SetResources($P, "Subtract", 4, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u04", "T1u04b"), $unitsTimer->setTo(1));

$P6->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u04"), Accumulate($P, "At Least", 4, "ore"))->then(CreateUnit($PT2, "Zerg Hydralisk", 3, "P6u"), SetResources($P, "Subtract", 4, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u04", "T2u04b"), $unitsTimer->setTo(1));
$P7->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u04"), Accumulate($P, "At Least", 4, "ore"))->then(CreateUnit($PT2, "Zerg Hydralisk", 3, "P7u"), SetResources($P, "Subtract", 4, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u04", "T2u04b"), $unitsTimer->setTo(1));
$P8->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u04"), Accumulate($P, "At Least", 4, "ore"))->then(CreateUnit($PT2, "Zerg Hydralisk", 3, "P8u"), SetResources($P, "Subtract", 4, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u04", "T2u04b"), $unitsTimer->setTo(1));

// 5: Tank 2 (1 siege mode & 1 tank mode)
$P1->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u05"), Accumulate($P, "At Least", 5, "ore"))->then(CreateUnit($PT1, "Terran Siege Tank (Tank Mode)", 1, "P1u"), CreateUnit($PT1, "Terran Siege Tank (Siege Mode)", 1, "P1u"), SetResources($P, "Subtract", 5, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u05", "T1u05b"), $unitsTimer->setTo(1));
$P3->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u05"), Accumulate($P, "At Least", 5, "ore"))->then(CreateUnit($PT1, "Terran Siege Tank (Tank Mode)", 1, "P3u"), CreateUnit($PT1, "Terran Siege Tank (Siege Mode)", 1, "P3u"), SetResources($P, "Subtract", 5, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u05", "T1u05b"), $unitsTimer->setTo(1));
$P4->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u05"), Accumulate($P, "At Least", 5, "ore"))->then(CreateUnit($PT1, "Terran Siege Tank (Tank Mode)", 1, "P4u"), CreateUnit($PT1, "Terran Siege Tank (Siege Mode)", 1, "P4u"), SetResources($P, "Subtract", 5, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u05", "T1u05b"), $unitsTimer->setTo(1));

$P6->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u05"), Accumulate($P, "At Least", 5, "ore"))->then(CreateUnit($PT2, "Terran Siege Tank (Tank Mode)", 1, "P6u"), CreateUnit($PT2, "Terran Siege Tank (Siege Mode)", 1, "P6u"), SetResources($P, "Subtract", 5, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u05", "T2u05b"), $unitsTimer->setTo(1));
$P7->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u05"), Accumulate($P, "At Least", 5, "ore"))->then(CreateUnit($PT2, "Terran Siege Tank (Tank Mode)", 1, "P7u"), CreateUnit($PT2, "Terran Siege Tank (Siege Mode)", 1, "P7u"), SetResources($P, "Subtract", 5, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u05", "T2u05b"), $unitsTimer->setTo(1));
$P8->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u05"), Accumulate($P, "At Least", 5, "ore"))->then(CreateUnit($PT2, "Terran Siege Tank (Tank Mode)", 1, "P8u"), CreateUnit($PT2, "Terran Siege Tank (Siege Mode)", 1, "P8u"), SetResources($P, "Subtract", 5, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u05", "T2u05b"), $unitsTimer->setTo(1));

// 6: Dragoons 3
$P1->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u06"), Accumulate($P, "At Least", 6, "ore"))->then(CreateUnit($PT1, "Protoss Dragoon", 3, "P1u"), SetResources($P, "Subtract", 6, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u06", "T1u06b"), $unitsTimer->setTo(1));
$P3->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u06"), Accumulate($P, "At Least", 6, "ore"))->then(CreateUnit($PT1, "Protoss Dragoon", 3, "P3u"), SetResources($P, "Subtract", 6, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u06", "T1u06b"), $unitsTimer->setTo(1));
$P4->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u06"), Accumulate($P, "At Least", 6, "ore"))->then(CreateUnit($PT1, "Protoss Dragoon", 3, "P4u"), SetResources($P, "Subtract", 6, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u06", "T1u06b"), $unitsTimer->setTo(1));

$P6->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u06"), Accumulate($P, "At Least", 6, "ore"))->then(CreateUnit($PT2, "Protoss Dragoon", 3, "P6u"), SetResources($P, "Subtract", 6, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u06", "T2u06b"), $unitsTimer->setTo(1));
$P7->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u06"), Accumulate($P, "At Least", 6, "ore"))->then(CreateUnit($PT2, "Protoss Dragoon", 3, "P7u"), SetResources($P, "Subtract", 6, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u06", "T2u06b"), $unitsTimer->setTo(1));
$P8->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u06"), Accumulate($P, "At Least", 6, "ore"))->then(CreateUnit($PT2, "Protoss Dragoon", 3, "P8u"), SetResources($P, "Subtract", 6, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u06", "T2u06b"), $unitsTimer->setTo(1));

// 7: Mutalisks 3
$P1->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u07"), Accumulate($P, "At Least", 7, "ore"))->then(CreateUnit($PT1, "Zerg Mutalisk", 3, "P1u"), SetResources($P, "Subtract", 7, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u07", "T1u07b"), $unitsTimer->setTo(1));
$P3->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u06"), Accumulate($P, "At Least", 7, "ore"))->then(CreateUnit($PT1, "Zerg Mutalisk", 3, "P3u"), SetResources($P, "Subtract", 7, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u07", "T1u07b"), $unitsTimer->setTo(1));
$P4->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u06"), Accumulate($P, "At Least", 7, "ore"))->then(CreateUnit($PT1, "Zerg Mutalisk", 3, "P4u"), SetResources($P, "Subtract", 7, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u07", "T1u07b"), $unitsTimer->setTo(1));

$P6->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u07"), Accumulate($P, "At Least", 7, "ore"))->then(CreateUnit($PT2, "Zerg Mutalisk", 3, "P6u"), SetResources($P, "Subtract", 7, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u07", "T2u07b"), $unitsTimer->setTo(1));
$P7->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u07"), Accumulate($P, "At Least", 7, "ore"))->then(CreateUnit($PT2, "Zerg Mutalisk", 3, "P7u"), SetResources($P, "Subtract", 7, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u07", "T2u07b"), $unitsTimer->setTo(1));
$P8->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u07"), Accumulate($P, "At Least", 7, "ore"))->then(CreateUnit($PT2, "Zerg Mutalisk", 3, "P8u"), SetResources($P, "Subtract", 7, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u07", "T2u07b"), $unitsTimer->setTo(1));

// 8: Wraiths 3
$P1->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u08"), Accumulate($P, "At Least", 8, "ore"))->then(CreateUnit($PT1, "Terran Wraith", 3, "P1u"), SetResources($P, "Subtract", 8, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u08", "T1u08b"), $unitsTimer->setTo(1));
$P3->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u08"), Accumulate($P, "At Least", 8, "ore"))->then(CreateUnit($PT1, "Terran Wraith", 3, "P3u"), SetResources($P, "Subtract", 8, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u08", "T1u08b"), $unitsTimer->setTo(1));
$P4->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u08"), Accumulate($P, "At Least", 8, "ore"))->then(CreateUnit($PT1, "Terran Wraith", 3, "P4u"), SetResources($P, "Subtract", 8, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u08", "T1u08b"), $unitsTimer->setTo(1));

$P6->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u08"), Accumulate($P, "At Least", 8, "ore"))->then(CreateUnit($PT2, "Terran Wraith", 3, "P6u"), SetResources($P, "Subtract", 8, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u08", "T2u08b"), $unitsTimer->setTo(1));
$P7->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u08"), Accumulate($P, "At Least", 8, "ore"))->then(CreateUnit($PT2, "Terran Wraith", 3, "P7u"), SetResources($P, "Subtract", 8, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u08", "T2u08b"), $unitsTimer->setTo(1));
$P8->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u08"), Accumulate($P, "At Least", 8, "ore"))->then(CreateUnit($PT2, "Terran Wraith", 3, "P8u"), SetResources($P, "Subtract", 8, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u08", "T2u08b"), $unitsTimer->setTo(1));

// 9: Scouts 3
$P1->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u09"), Accumulate($P, "At Least", 9, "ore"))->then(CreateUnit($PT1, "Protoss Scout", 3, "P1u"), SetResources($P, "Subtract", 9, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u09", "T1u09b"), $unitsTimer->setTo(1));
$P3->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u09"), Accumulate($P, "At Least", 9, "ore"))->then(CreateUnit($PT1, "Protoss Scout", 3, "P3u"), SetResources($P, "Subtract", 9, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u09", "T1u09b"), $unitsTimer->setTo(1));
$P4->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u09"), Accumulate($P, "At Least", 9, "ore"))->then(CreateUnit($PT1, "Protoss Scout", 3, "P4u"), SetResources($P, "Subtract", 9, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u09", "T1u09b"), $unitsTimer->setTo(1));

$P6->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u09"), Accumulate($P, "At Least", 9, "ore"))->then(CreateUnit($PT2, "Protoss Scout", 3, "P6u"), SetResources($P, "Subtract", 9, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u09", "T2u09b"), $unitsTimer->setTo(1));
$P7->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u09"), Accumulate($P, "At Least", 9, "ore"))->then(CreateUnit($PT2, "Protoss Scout", 3, "P7u"), SetResources($P, "Subtract", 9, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u09", "T2u09b"), $unitsTimer->setTo(1));
$P8->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u09"), Accumulate($P, "At Least", 9, "ore"))->then(CreateUnit($PT2, "Protoss Scout", 3, "P8u"), SetResources($P, "Subtract", 9, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u09", "T2u09b"), $unitsTimer->setTo(1));

// 10: Ultralisk 2
$P1->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u10"), Accumulate($P, "At Least", 10, "ore"))->then(CreateUnit($PT1, "Zerg Ultralisk", 2, "P1u"), SetResources($P, "Subtract", 10, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u10", "T1u10b"), $unitsTimer->setTo(1));
$P3->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u10"), Accumulate($P, "At Least", 10, "ore"))->then(CreateUnit($PT1, "Zerg Ultralisk", 2, "P3u"), SetResources($P, "Subtract", 10, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u10", "T1u10b"), $unitsTimer->setTo(1));
$P4->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u10"), Accumulate($P, "At Least", 10, "ore"))->then(CreateUnit($PT1, "Zerg Ultralisk", 2, "P4u"), SetResources($P, "Subtract", 10, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u10", "T1u10b"), $unitsTimer->setTo(1));

$P6->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u10"), Accumulate($P, "At Least", 10, "ore"))->then(CreateUnit($PT2, "Zerg Ultralisk", 2, "P6u"), SetResources($P, "Subtract", 10, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u10", "T2u10b"), $unitsTimer->setTo(1));
$P7->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u10"), Accumulate($P, "At Least", 10, "ore"))->then(CreateUnit($PT2, "Zerg Ultralisk", 2, "P7u"), SetResources($P, "Subtract", 10, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u10", "T2u10b"), $unitsTimer->setTo(1));
$P8->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u10"), Accumulate($P, "At Least", 10, "ore"))->then(CreateUnit($PT2, "Zerg Ultralisk", 2, "P8u"), SetResources($P, "Subtract", 10, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u10", "T2u10b"), $unitsTimer->setTo(1));

// 11: Goliath 4
$P1->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u11"), Accumulate($P, "At Least", 11, "ore"))->then(CreateUnit($PT1, "Terran Goliath", 4, "P1u"), SetResources($P, "Subtract", 11, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u11", "T1u11b"), $unitsTimer->setTo(1));
$P3->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u11"), Accumulate($P, "At Least", 11, "ore"))->then(CreateUnit($PT1, "Terran Goliath", 4, "P3u"), SetResources($P, "Subtract", 11, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u11", "T1u11b"), $unitsTimer->setTo(1));
$P4->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u11"), Accumulate($P, "At Least", 11, "ore"))->then(CreateUnit($PT1, "Terran Goliath", 4, "P4u"), SetResources($P, "Subtract", 11, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u11", "T1u11b"), $unitsTimer->setTo(1));

$P6->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u11"), Accumulate($P, "At Least", 11, "ore"))->then(CreateUnit($PT2, "Terran Goliath", 4, "P6u"), SetResources($P, "Subtract", 11, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u11", "T2u11b"), $unitsTimer->setTo(1));
$P7->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u11"), Accumulate($P, "At Least", 11, "ore"))->then(CreateUnit($PT2, "Terran Goliath", 4, "P7u"), SetResources($P, "Subtract", 11, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u11", "T2u11b"), $unitsTimer->setTo(1));
$P8->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u11"), Accumulate($P, "At Least", 11, "ore"))->then(CreateUnit($PT2, "Terran Goliath", 4, "P8u"), SetResources($P, "Subtract", 11, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u11", "T2u11b"), $unitsTimer->setTo(1));

// 12: Archon 3
$P1->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u12"), Accumulate($P, "At Least", 12, "ore"))->then(CreateUnit($PT1, "Protoss Archon", 3, "P1u"), SetResources($P, "Subtract", 12, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u12", "T1u12b"), $unitsTimer->setTo(1));
$P3->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u12"), Accumulate($P, "At Least", 12, "ore"))->then(CreateUnit($PT1, "Protoss Archon", 3, "P3u"), SetResources($P, "Subtract", 12, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u12", "T1u12b"), $unitsTimer->setTo(1));
$P4->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u12"), Accumulate($P, "At Least", 12, "ore"))->then(CreateUnit($PT1, "Protoss Archon", 3, "P4u"), SetResources($P, "Subtract", 12, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u12", "T1u12b"), $unitsTimer->setTo(1));

$P6->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u12"), Accumulate($P, "At Least", 12, "ore"))->then(CreateUnit($PT2, "Protoss Archon", 3, "P6u"), SetResources($P, "Subtract", 12, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u12", "T2u12b"), $unitsTimer->setTo(1));
$P7->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u12"), Accumulate($P, "At Least", 12, "ore"))->then(CreateUnit($PT2, "Protoss Archon", 3, "P7u"), SetResources($P, "Subtract", 12, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u12", "T2u12b"), $unitsTimer->setTo(1));
$P8->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u12"), Accumulate($P, "At Least", 12, "ore"))->then(CreateUnit($PT2, "Protoss Archon", 3, "P8u"), SetResources($P, "Subtract", 12, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u12", "T2u12b"), $unitsTimer->setTo(1));

// 13: Guardian 4
$P1->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u13"), Accumulate($P, "At Least", 13, "ore"))->then(CreateUnit($PT1, "Zerg Guardian", 4, "P1u"), SetResources($P, "Subtract", 13, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u13", "T1u13b"), $unitsTimer->setTo(1));
$P3->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u13"), Accumulate($P, "At Least", 13, "ore"))->then(CreateUnit($PT1, "Zerg Guardian", 4, "P3u"), SetResources($P, "Subtract", 13, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u13", "T1u13b"), $unitsTimer->setTo(1));
$P4->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u13"), Accumulate($P, "At Least", 13, "ore"))->then(CreateUnit($PT1, "Zerg Guardian", 4, "P4u"), SetResources($P, "Subtract", 13, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u13", "T1u13b"), $unitsTimer->setTo(1));

$P6->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u13"), Accumulate($P, "At Least", 13, "ore"))->then(CreateUnit($PT2, "Zerg Guardian", 4, "P6u"), SetResources($P, "Subtract", 13, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u13", "T2u13b"), $unitsTimer->setTo(1));
$P7->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u13"), Accumulate($P, "At Least", 13, "ore"))->then(CreateUnit($PT2, "Zerg Guardian", 4, "P7u"), SetResources($P, "Subtract", 13, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u13", "T2u13b"), $unitsTimer->setTo(1));
$P8->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u13"), Accumulate($P, "At Least", 13, "ore"))->then(CreateUnit($PT2, "Zerg Guardian", 4, "P8u"), SetResources($P, "Subtract", 13, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u13", "T2u13b"), $unitsTimer->setTo(1));

// 14: Battlecruiser 2
$P1->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u14"), Accumulate($P, "At Least", 14, "ore"))->then(CreateUnit($PT1, "Terran Battlecruiser", 2, "P1u"), SetResources($P, "Subtract", 13, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u14", "T1u14b"), $unitsTimer->setTo(1));
$P3->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u14"), Accumulate($P, "At Least", 14, "ore"))->then(CreateUnit($PT1, "Terran Battlecruiser", 2, "P3u"), SetResources($P, "Subtract", 13, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u14", "T1u14b"), $unitsTimer->setTo(1));
$P4->_if(Bring($P, "Protoss Probe", "At Least", 1, "T1u14"), Accumulate($P, "At Least", 14, "ore"))->then(CreateUnit($PT1, "Terran Battlecruiser", 2, "P4u"), SetResources($P, "Subtract", 13, "ore"), MoveUnit($P, "Protoss Probe", 1, "T1u14", "T1u14b"), $unitsTimer->setTo(1));

$P6->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u14"), Accumulate($P, "At Least", 14, "ore"))->then(CreateUnit($PT2, "Terran Battlecruiser", 2, "P6u"), SetResources($P, "Subtract", 14, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u14", "T2u14b"), $unitsTimer->setTo(1));
$P7->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u14"), Accumulate($P, "At Least", 14, "ore"))->then(CreateUnit($PT2, "Terran Battlecruiser", 2, "P7u"), SetResources($P, "Subtract", 14, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u14", "T2u14b"), $unitsTimer->setTo(1));
$P8->_if(Bring($P, "Protoss Probe", "At Least", 1, "T2u14"), Accumulate($P, "At Least", 14, "ore"))->then(CreateUnit($PT2, "Terran Battlecruiser", 2, "P8u"), SetResources($P, "Subtract", 14, "ore"), MoveUnit($P, "Protoss Probe", 1, "T2u14", "T2u14b"), $unitsTimer->setTo(1));



// Welcome message
$Players->_if($messagetimer->exactly(60))->then(
		Display('\x013\x01fW'), '');
$Players->_if($messagetimer->exactly(58))->then(
		ClearText(),
		Display('\x013\x01eW\x01fe'), '');
$Players->_if($messagetimer->exactly(56))->then(
		ClearText(),
		Display('\x013\x01dW\x01ee\x01fl'), '');
$Players->_if($messagetimer->exactly(54))->then(
		ClearText(),
		Display('\x013\x01cW\x01de\x01el\x01fc'), '');
$Players->_if($messagetimer->exactly(52))->then(
		ClearText(),
		Display('\x013\x01bW\x01ce\x01el\x01dc\x01fo'), '');
$Players->_if($messagetimer->exactly(50))->then(
		ClearText(),
		Display('\x013\x01aW\x01be\x01cl\x01dc\x01eo\x01fm'), '');
$Players->_if($messagetimer->exactly(48))->then(
		ClearText(),
		Display('\x013\x019W\x01ae\x01bl\x01cc\x01do\x01em\x01fe'), '');
$Players->_if($messagetimer->exactly(46))->then(
		ClearText(),
		Display('\x013\x018W\x019e\x01al\x01bc\x01co\x01dm\x01ee \x01ft'), '');
$Players->_if($messagetimer->exactly(44))->then(
		ClearText(),
		Display('\x013\x017W\x018e\x019l\x01ac\x01bo\x01cm\x01de \x01et\x01fo'), '');
$Players->_if($messagetimer->exactly(42))->then(
		ClearText(),
		Display('\x013\x016W\x017e\x018l\x019c\x01ao\x01bm\x01ce \x01dt\x01eo \x01fS'), '');
$Players->_if($messagetimer->exactly(40))->then(
		ClearText(),
		Display('\x013\x015W\x016e\x017l\x018c\x019o\x01am\x01be \x01ct\x01do \x01eS\x01ft'), '');
$Players->_if($messagetimer->exactly(38))->then(
		ClearText(),
		Display('\x013\x011W\x015e\x016l\x017c\x018o\x019m\x01ae \x01bt\x01co \x01dS\x01et\x01fa'), '');
$Players->_if($messagetimer->exactly(36))->then(
		ClearText(),
		Display('\x013\x010W\x011e\x015l\x016c\x017o\x018m\x019e \x01at\x01bo \x01cS\x01dt\x01ea\x01fr'), '');
$Players->_if($messagetimer->exactly(34))->then(
		ClearText(),
		Display('\x013\x00fW\x010e\x011l\x015c\x016o\x017m\x018e \x019t\x01ao \x01bS\x01ct\x01da\x01er\x01fC'), '');
$Players->_if($messagetimer->exactly(32))->then(
		ClearText(),
		Display('\x013\x00eW\x00fe\x010l\x011c\x015o\x016m\x017e \x018t\x019o \x01aS\x01bt\x01ca\x01dr\x01eC\x01fl'), '');
$Players->_if($messagetimer->exactly(30))->then(
		ClearText(),
		Display('\x013\x008W\x00ee\x00fl\x010c\x011o\x015m\x016e \x017t\x018o \x019S\x01at\x01ba\x01cr\x01dC\x01el\x01fa'), '');
$Players->_if($messagetimer->exactly(28))->then(
		ClearText(),
		Display('\x013\x007W\x008e\x00el\x00fc\x010o\x011m\x015e \x016t\x017o \x018S\x019t\x01aa\x01br\x01cC\x01dl\x01ea\x01fs'), '');
$Players->_if($messagetimer->exactly(26))->then(
		ClearText(),
		Display('\x013\x006W\x007e\x008l\x00ec\x00fo\x010m\x011e \x015t\x016o \x017S\x018t\x019a\x01ar\x01bC\x01cl\x01da\x01es\x01fh'), '');
$Players->_if($messagetimer->exactly(24))->then(
		ClearText(),
		Display('\x013\x006W\x007e\x008l\x00ec\x00fo\x010m\x011e \x015t\x016o \x017S\x018t\x019a\x01ar\x01bC\x01cl\x01da\x01es\x01fh \x002!\x005!'), '');
$Players->_if($messagetimer->exactly(22))->then(
		ClearText(),
		Display('\x013\x006W\x007e\x008l\x00ec\x00fo\x010m\x011e \x015t\x016o \x017S\x018t\x019a\x01ar\x01bC\x01cl\x01da\x01es\x01fh \x004!\x003!\x001!'), '');



?>