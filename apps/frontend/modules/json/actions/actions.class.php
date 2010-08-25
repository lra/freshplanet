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

	/**
	 * Retrieve the full game board state with JSON
	 */
	public function executeGetFullGameboard(sfWebRequest $request)
	{
		$data = array();
		$data['result'] = Board::GAME_ERROR;

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
					$state = $tile->getState();
					if ($state === Tile::PUB_EMPTY)
					{
						$state += $board->getMinesAround($offset);
					}
					$data['board'][] = array(
						'offset' => $offset,
						'state' => $state);
					$offset++;
				}
			}
		}

		return $this->renderComponent('json', 'json', array('data' => $data));
	}
	
	/**
	 * Click a tile and retrieve the state of the changed tiles with JSON
	 */
	public  function executeClickTile(sfWebRequest $request)
	{
		$data = array();
		$data['result'] = Board::GAME_ERROR;
		$offset = $request->getParameter('offset');

		$user = $this->getUser();
		$dbUser = Doctrine_Core::getTable('User')->find($user->getAttribute('id'));
		if (get_class($dbUser) === 'User')
		{
			// Load the game board
			$board = new Board($dbUser->getGameBoard());
			if (get_class($board) === 'Board')
			{
				$data['result'] = $board->leftClick($offset);
				$dbUser->setGameBoard($board->dump());
				$dbUser->save();
				$data['board'] = array();

				$current_offset = 0;
				foreach ($board->getTiles() as $tile)
				{
					$state = $tile->getState();
					if ($state === Tile::PUB_EMPTY)
					{
						$state += $board->getMinesAround($current_offset);
					}
					$data['board'][] = array('offset' => $current_offset,
																	 'state' => $state);
					$current_offset++;
				}
			}
		}
		
		return $this->renderComponent('json', 'json', array('data' => $data));		
	}

	/**
	 * Flag/unflag a tile and retrieve its state with JSON
	 */
	public  function executeFlagTile(sfWebRequest $request)
	{
		$data = array();
		$data['result'] = Board::GAME_ERROR;
		$offset = $request->getParameter('offset');

		$user = $this->getUser();
		$dbUser = Doctrine_Core::getTable('User')->find($user->getAttribute('id'));
		if (get_class($dbUser) === 'User')
		{
			// Load the game board
			$board = new Board($dbUser->getGameBoard());
			if (get_class($board) === 'Board')
			{
				$data['result'] = $board->flagTile($offset);
				$dbUser->setGameBoard($board->dump());
				$dbUser->save();
				$data['board'] = array();
				$data['board'][] = array
				(
					'offset' => $offset,
					// 'state' => $tile->getState());
					'state' => $board->getTile($offset)->getState()
				);
			}
		}
		
		return $this->renderComponent('json', 'json', array('data' => $data));		
	}

	/**
	 * Question/unquestion a tile and retrieve its state with JSON
	 */
	public  function executeQuestionTile(sfWebRequest $request)
	{
		$data = array();
		$data['result'] = Board::GAME_ERROR;
		$offset = $request->getParameter('offset');

		$user = $this->getUser();
		$dbUser = Doctrine_Core::getTable('User')->find($user->getAttribute('id'));
		if (get_class($dbUser) === 'User')
		{
			// Load the game board
			$board = new Board($dbUser->getGameBoard());
			if (get_class($board) === 'Board')
			{
				$data['result'] = $board->questionTile($offset);
				$dbUser->setGameBoard($board->dump());
				$dbUser->save();
				$data['board'] = array();
				$data['board'][] = array
				(
					'offset' => $offset,
					// 'state' => $tile->getState());
					'state' => $board->getTile($offset)->getState()
				);
			}
		}
		
		return $this->renderComponent('json', 'json', array('data' => $data));		
	}
}
