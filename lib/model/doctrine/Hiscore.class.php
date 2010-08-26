<?php

/**
 * Hiscore
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    minesweeper
 * @subpackage model
 * @author     Laurent Raufaste
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Hiscore extends BaseHiscore
{
	static public function saveScore($dbUser)
	{
		if (get_class($dbUser) === 'User')
		{
			$binary_board = $dbUser->getGameBoard();
			if (!is_string($binary_board))
			{
				throw new Exception('No game board found for this user');
			}
			$board = new Board($binary_board);
			if (get_class($board) !== 'Board')
			{
				throw new Exception('Unable to load the game board');
			}
		}
		else
		{
			throw new Exception('Invalid user');
		}

		$h = new Hiscore();
		$h->setUser($dbUser);

		$seconds = time() - $dbUser->getGameStart();
		if ($seconds <= 0)
		{
			throw new Exception('Negative time record');
		}
		$h->setTime($seconds);
		$h->setBoardwidth($board->getWidth());
		$h->save();
	}
}
