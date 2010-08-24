<?php

/**
 * json actions.
 *
 * @package    minesweeper
 * @subpackage json
 * @author     Laurent Raufaste
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class jsonActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
		$data = array('1' => 'un', 'deux' => 2);

		return $this->renderComponent('json', 'json', array('data' => $data));
  }

	public function executeGetFullGameboard(sfWebRequest $request)
	{
		$data = array();

		// Load the user
		$user = $this->getUser();
		$dbUser = Doctrine_Core::getTable('User')->find($user->getAttribute('id'));
		if (get_class($dbUser) === 'User')
		{
			// Load the game board
			$board = new Board($dbUser->getGameBoard());
			if (get_class($board) === 'Board')
			{
				$data['result'] = Board::GAME_NOTHING;
				$data['board'] = array();
				$offset = 0;
				foreach ($board->getTiles() as $tile)
				{
					$data['board'][] = array('offset' => $offset,
																	 // 'state' => $tile->getState());
																	 'state' => rand(1, 5));
					$offset++;
				}
			}
		}

		return $this->renderComponent('json', 'json', array('data' => $data));
	}
}
