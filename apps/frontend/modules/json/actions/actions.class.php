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
				if ($board->isLost())
				{
					$data['result'] = Board::GAME_LOST;
				}
				elseif ($board->isWon())
				{
					$data['result'] = Board::GAME_WON;
				}
				else
				{
					$data['result'] = Board::GAME_NOTHING;
				}
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
		// Initialization of variables
		$data = array();
		$data['result'] = Board::GAME_ERROR;
		$offset = $request->getParameter('offset');
		$tiles_before_click = array();

		// Try to load the current user
		$user = $this->getUser();
		$dbUser = Doctrine_Core::getTable('User')->find($user->getAttribute('id'));
		if (get_class($dbUser) === 'User')
		{
			// Load the game board of the user
			$board = new Board($dbUser->getGameBoard());
			if (get_class($board) === 'Board')
			{
				// Save the state of every tile before the user interaction
				foreach ($board->getTiles() as $tile)
				{
					$tiles_before_click[] = $tile->getState();
				}

				// Reveal the tile chosen by the user (and the others tiles)
				$data['result'] = $board->revealTile($offset);

				// Save the new gameboard
				$dbUser->setGameBoard($board->dump());
				$dbUser->save();

				// Put the new tiles status in the board JSON array
				$data['board'] = array();
				$current_offset = 0;
				foreach ($board->getTiles() as $k => $tile)
				{
					// Get the state of the current tile
					$state = $tile->getState();

					// If the state of the tile changed due to the user click, keep it
					if ($state !== $tiles_before_click[$k])
					{
						// If the tile is empty, determine the number of mines around it
						if ($state === Tile::PUB_EMPTY)
						{
							// Display the number of mines on the tile
							$state += $board->getMinesAround($current_offset);
						}

						// Add the tile data to the JSON array
						$data['board'][] = array('offset' => $current_offset,
																		 'state' => $state);
					}
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
