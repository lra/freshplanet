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
$t = new lime_test(2);

/**
 * The tests
 */

$b = Board::generate(5);

$t->is(get_class($b), 'Board');

$t->is($b->getSize(), 25);
