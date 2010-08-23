<?php

/**
 * Tests initialization
 */
require_once (dirname(__FILE__).'/../bootstrap/unit.php');
$configuration = ProjectConfiguration::getApplicationConfiguration( 'frontend', 'test', true);
new sfDatabaseManager($configuration);

/**
 * Number of tests to do
 */
$t = new lime_test(31);

/**
 * The tests
 */

$tile = new Tile();
$t->is($tile->getValue(), Tile::STATE_DEFAULT);

$t->ok(!$tile->isMined());
$t->ok($tile->isUntouched());
$t->ok(!$tile->isFlagged());
$t->ok(!$tile->isQuestioned());
$t->ok(!$tile->isRevealed());

$tile->setQuestioned();

$t->ok(!$tile->isMined());
$t->ok(!$tile->isUntouched());
$t->ok(!$tile->isFlagged());
$t->ok($tile->isQuestioned());
$t->ok(!$tile->isRevealed());

$tile->setRevealed();

$t->ok(!$tile->isMined());
$t->ok(!$tile->isUntouched());
$t->ok(!$tile->isFlagged());
$t->ok(!$tile->isQuestioned());
$t->ok($tile->isRevealed());


$tile->setMined();

$t->ok($tile->isMined());
$t->ok(!$tile->isUntouched());
$t->ok(!$tile->isFlagged());
$t->ok(!$tile->isQuestioned());
$t->ok($tile->isRevealed());

$tile->setFlagged();

$t->ok($tile->isMined());
$t->ok(!$tile->isUntouched());
$t->ok($tile->isFlagged());
$t->ok(!$tile->isQuestioned());
$t->ok(!$tile->isRevealed());

$tile->setUntouched();

$t->ok($tile->isMined());
$t->ok($tile->isUntouched());
$t->ok(!$tile->isFlagged());
$t->ok(!$tile->isQuestioned());
$t->ok(!$tile->isRevealed());
