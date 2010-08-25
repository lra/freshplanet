<?php

/**
 * game actions.
 *
 * @package    minesweeper
 * @subpackage game
 * @author     Laurent Raufaste
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class gameActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
		$user = $this->getUser();
		$dbUser = Doctrine_Core::getTable('User')->find($user->getAttribute('id'));
		if (get_class($dbUser) === 'User')
		{
			$binary_board = $dbUser->getGameBoard();
			if (!is_string($binary_board))
			{
				$user->setFlash('error', 'No game board yet');
				$this->redirect('user/index');
			}
			$this->board = new Board($binary_board);
			if (get_class($this->board) !== 'Board')
			{
				$user->setFlash('error', 'Unable to load the game board');
				$this->redirect('user/index');
			}
		}
		else
		{
			$user->setFlash('error', 'Unable to find the user');
			$this->redirect('user/index');
		}
  }
  
  public function executeNew(sfWebRequest $request)
  {
		// This action should only be accessed the user form
		$this->forward404Unless($request->isMethod('post'));

		$width = intval($request->getParameter('width'));
		$user = $this->getUser();

		// Create a new board
		$board = Board::generate($width);
		if (get_class($board) !== 'Board')
		{
			$user->setFlash('error', 'Unable to create the game board');
			$this->redirect('user/index');
		}

		// Try to find the user
		$dbUser = Doctrine_Core::getTable('User')->find($user->getAttribute('id'));
		if (get_class($dbUser) === 'User')
		{
			// Saves the game board
			$dbUser->setGameBoard($board->dump());
			
			// Saves the current time
			$dbUser->setGameStart(time());
			$dbUser->save();
			$this->redirect('game/index');
		}
		else
		{
			$user->setFlash('error', 'Unable to find the user');
			$this->redirect('user/index');
		}
  }
}
