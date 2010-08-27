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
	 * Click a tile and retrieve the state of the changed tiles with JSON
	 */
	public  function executeClickTile(sfWebRequest $request)
	{
		// Initialization of variables
		$data = array();
		$data['r'] = Board::GAME_ERROR;
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
				$data['r'] = $board->revealTile($offset);
				if ($data['r'] === Board::GAME_WON)
				{
					// Save the hiscore
					Hiscore::saveScore($dbUser);
				}

				// Save the new gameboard
				$dbUser->setGameBoard($board->dump());
				$dbUser->save();

				// Put the new tiles status in the board JSON array
				$data['b'] = array();
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
						$data['b'][] = array('o' => $current_offset,
																		 's' => $state);
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
		$data['r'] = Board::GAME_ERROR;
		$offset = $request->getParameter('offset');

		$user = $this->getUser();
		$dbUser = Doctrine_Core::getTable('User')->find($user->getAttribute('id'));
		if (get_class($dbUser) === 'User')
		{
			// Load the game board
			$board = new Board($dbUser->getGameBoard());
			if (get_class($board) === 'Board')
			{
				$data['r'] = $board->flagTile($offset);
				if ($data['r'] === Board::GAME_WON)
				{
					// Save the hiscore
					Hiscore::saveScore($dbUser);
				}
				$dbUser->setGameBoard($board->dump());
				$dbUser->save();
				$data['b'] = array();
				$data['b'][] = array
				(
					'o' => $offset,
					's' => $board->getTile($offset)->getState()
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
		$data['r'] = Board::GAME_ERROR;
		$offset = $request->getParameter('offset');

		$user = $this->getUser();
		$dbUser = Doctrine_Core::getTable('User')->find($user->getAttribute('id'));
		if (get_class($dbUser) === 'User')
		{
			// Load the game board
			$board = new Board($dbUser->getGameBoard());
			if (get_class($board) === 'Board')
			{
				$data['r'] = $board->questionTile($offset);
				$dbUser->setGameBoard($board->dump());
				$dbUser->save();
				$data['b'] = array();
				$data['b'][] = array
				(
					'o' => $offset,
					's' => $board->getTile($offset)->getState()
				);
			}
		}
		
		return $this->renderComponent('json', 'json', array('data' => $data));		
	}
}
