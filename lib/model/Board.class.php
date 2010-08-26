<?php

class Board
{
	/**
	 * Constants
	 */

	const GAME_ERROR = 1;
	const GAME_NOTHING = 2;
	const GAME_FLAGGED = 3;
	const GAME_UNFLAGGED = 4;
	const GAME_QUESTIONED = 5;
	const GAME_UNQUESTIONED = 6;
	const GAME_DISCOVERED = 7;
	const GAME_WON = 8;
	const GAME_LOST = 9;

	private $tiles;

	/**
	 * Static public functions
	 */

	static public function generate($boardwidth)
	{
		if (!is_int($boardwidth))
		{
			throw new Exception('An integer is required');
		}

		$boardsize = $boardwidth * $boardwidth;

		if ($boardsize < sfConfig::get('app_board_minwidth')*sfConfig::get('app_board_minwidth')
				|| $boardsize > sfConfig::get('app_board_maxwidth')*sfConfig::get('app_board_maxwidth'))
		{
			throw new Exception('Invalid board size: '.$boardsize);
		}

		$binary_data = '';

		for ($i = 0; $i < $boardsize; $i++)
		{
			$tile = new Tile();
			if (sfConfig::get('app_board_minepercentage') >= rand(1, 100))
			{
				$tile->setMined();
			}
			//$binary_data .= pack('C', $tile->getValue());
			$binary_data .= strval($tile->getValue());
		}

		return new Board($binary_data);
	}

	/**
	 * Public functions
	 */

	public function __construct($binary_data)
	{
		if (!is_string($binary_data))
		{
			throw new Exception('A string containing the binary data is required');
		}

		//$tiles_data = unpack('C*', $binary_data);
		$tiles_data = array();
		for ($i = 0; $i < strlen($binary_data); $i++)
		{
			$tiles_data[] = intval($binary_data[$i]);
		}

		foreach ($tiles_data as $tile_value)
		{
			$this->tiles[] = new Tile($tile_value);
		}
	}

	public function getWidth()
	{
		$width = sqrt($this->getSize());

		if ($width != floor($width))
		{
			throw new Exception('Invalid board size: '.$this->getSize());
		}

		return $width;
	}
	
	public function getSize()
	{
		return count($this->tiles);
	}

	public function dump()
	{
		$binary_data = '';

		foreach ($this->tiles as $tile)
		{
			// $binary_data .= pack('C', $tile->getValue());
			$binary_data .= strval($tile->getValue());
		}
		
		return $binary_data;
	}

	public function getTile($offset)
	{
		if (!isset($this->tiles[$offset]))
		{
			throw new Exception('Tile not found');
		}

		return $this->tiles[$offset];
	}

	public function getTiles()
	{
		return $this->tiles;
	}
	
	public function revealTile($offset)
	{
		if (!isset($this->tiles[$offset]))
		{
			throw new Exception('Tile not found');
		}
	
		$return = Board::GAME_ERROR;

		$tile = $this->tiles[$offset];
		if (!$tile->isRevealed())
		{
			// It is a mine, you loose
			if ($tile->isMined())
			{
				$this->revealMap();
				$return = Board::GAME_LOST;
			}
			else
			{
				// It is not a mine, next
				$tile->setRevealed();			

				// Reveal the tiles surrounding this one
				if ($this->getMinesAround($offset) === 0)
				{
					$this->revealTilesAround($offset);
				}
				
				$return = Board::GAME_DISCOVERED;

				if ($this->isWon())
				{
					$return = Board::GAME_WON;
				}
			}
		}

		return $return;
	}

	public function revealTilesAround($offset)
	{
		if (!isset($this->tiles[$offset]))
		{
			throw new Exception('Tile not found');
		}

		$width = $this->getWidth();
		$x_pos = intval($offset % $width);
		$y_pos = intval($offset / $width);
		$to_check = array();
		
		// Pos 1
		if ($x_pos > 0 && $y_pos > 0)
		{
			$to_check[] = $offset - $width - 1;
		}

		// Pos 2
		if ($y_pos > 0)
		{
			$to_check[] = $offset - $width;
		}

		// Pos 3
		if (($x_pos < $width - 1) && $y_pos > 0)
		{
			$to_check[] = $offset - $width + 1;
		}

		// Pos 4
		if ($x_pos > 0)
		{
			$to_check[] = $offset - 1;
		}

		// Pos 6
		if ($x_pos < $width - 1)
		{
			$to_check[] = $offset + 1;
		}

		// Pos 7
		if ($x_pos > 0 && ($y_pos < $width - 1))
		{
			$to_check[] = $offset + $width - 1;
		}

		// Pos 8
		if ($y_pos < $width - 1)
		{
			$to_check[] = $offset + $width;
		}

		// Pos 9
		if (($x_pos < $width - 1) && ($y_pos < $width - 1))
		{
			$to_check[] = $offset + $width + 1;
		}

		foreach ($to_check as $c)
		{
			if (!$this->tiles[$c]->isRevealed())
			{
				$this->revealTile($c);
			}
		}
	}

	/**
	 * Check if the gameboard have been fully resolved
	 * For each tile:
	 *  - if the tile is not mined then its state must be revealed)
	 *  - if the tile is mined then its state must be flagged)
	 * @return boolean True if the board has been won
	 */
	public function isWon()
	{
		foreach ($this->getTiles() as $tile)
		{
			if ($tile->isMined())
			{
				if (!$tile->isFlagged())
				{
					return false;
				}
			}
			else
			{
				if (!$tile->isRevealed())
				{
					return false;
				}
			}
		}

		return true;
	}
	
	public function isLost()
	{
		foreach ($this->getTiles() as $tile)
		{
			if ($tile->isMined() && $tile->isRevealed())
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Reveals the whole map
	 */
	
	public function revealMap()
	{
		foreach ($this->getTiles() as $tile)
		{
			if (!$tile->isRevealed())
			{
				$tile->setRevealed();
			}
		}
	}

	/**
	 * Returns the number of mines around the specified tile
	 * Need to check at most those 8 tiles:
	 * 123
	 * 4 6
	 * 789
	 */

	public function getMinesAround($offset)
	{
		$nb_mines = 0;
		$width = $this->getWidth();
		$x_pos = intval($offset % $width);
		$y_pos = intval($offset / $width);
		$to_check = array();
		
		// Pos 1
		if ($x_pos > 0 && $y_pos > 0)
		{
			$to_check[] = $offset - $width - 1;
		}

		// Pos 2
		if ($y_pos > 0)
		{
			$to_check[] = $offset - $width;
		}

		// Pos 3
		if (($x_pos < $width - 1) && $y_pos > 0)
		{
			$to_check[] = $offset - $width + 1;
		}

		// Pos 4
		if ($x_pos > 0)
		{
			$to_check[] = $offset - 1;
		}

		// Pos 6
		if ($x_pos < $width - 1)
		{
			$to_check[] = $offset + 1;
		}

		// Pos 7
		if ($x_pos > 0 && ($y_pos < $width - 1))
		{
			$to_check[] = $offset + $width - 1;
		}

		// Pos 8
		if ($y_pos < $width - 1)
		{
			$to_check[] = $offset + $width;
		}

		// Pos 9
		if (($x_pos < $width - 1) && ($y_pos < $width - 1))
		{
			$to_check[] = $offset + $width + 1;
		}

		foreach ($to_check as $c)
		{
			if ($this->tiles[$c]->isMined())
			{
				$nb_mines++;
			}
		}

		return $nb_mines;
	}

	public function flagTile($offset)
	{
		if (!isset($this->tiles[$offset]))
		{
			throw new Exception('Tile not found');
		}

		$return = Board::GAME_ERROR;

		$tile = $this->tiles[$offset];
		if (!$tile->isRevealed())
		{
			if ($tile->isFlagged())
			{
				$tile->setUntouched();
				$return = Board::GAME_UNFLAGGED;
			}
			else
			{
				$tile->setFlagged();
				if ($this->isWon())
				{
					$return = Board::GAME_WON;
				}
				else
				{
					$return = Board::GAME_FLAGGED;
				}
			}
		}

		return $return;
	}

	public function questionTile($offset)
	{
		if (!isset($this->tiles[$offset]))
		{
			throw new Exception('Tile not found');
		}

		$return = Board::GAME_ERROR;

		$tile = $this->tiles[$offset];
		if (!$tile->isRevealed())
		{
			if ($tile->isQuestioned())
			{
				$tile->setUntouched();
				$return = Board::GAME_UNQUESTIONED;
			}
			else
			{
				$tile->setQuestioned();
				$return = Board::GAME_QUESTIONED;
			}
		}

		return $return;
	}
}
