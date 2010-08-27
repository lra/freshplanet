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

	const NO_MORE_TILE = '1000';

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

		$binary_data = array();

		for ($i = 0; $i < $boardsize; $i++)
		{
			$tile = new Tile();
			if (sfConfig::get('app_board_minepercentage') >= rand(1, 100))
			{
				$tile->setMined();
			}
			$binary_data[] = $tile->getValue();
		}

		$bin_string = Board::arrayToBinString($binary_data);

		return new Board($bin_string);
	}

	/**
	 * Private functions
	 */

	static private function padByteByLeft($byte, $pad)
	{
		if (!is_string($byte))
		{
			return '';
		}

		while (strlen($byte) < $pad)
		{
			$byte = '0'.$byte;
		}

		return $byte;
	}

	static private function arrayToBinString($data)
	{
		if (!is_array($data))
		{
			return '';
		}

		$bytes = '';

		for ($i = 0; $i < count($data); $i += 2)
		{
			$bytes .= Board::padByteByLeft(decbin($data[$i]), 4);

			if (($i+1) < count($data))
			{
				$bytes .= Board::padByteByLeft(decbin($data[$i+1]), 4);
			}
			else
			{
				$bytes .= Board::NO_MORE_TILE;
			}
		}

		if (!is_string($bytes) || (strlen($bytes) % 8))
		{
			return '';
		}

		$string = '';

		$nb_bits = intval(strlen($bytes));

		for ($i = 0; $i < $nb_bits; $i += 8)
		{
			$byte = '';
			for ($j = 0; $j < 8; $j++)
			{
				$byte .= $bytes[$i+$j];
			}
			$string .= chr(bindec($byte));
		}

		return gzdeflate($string, 9);
	}

	static private function binStringToArray($string)
	{
		$bytes = array();;
		if (!is_string($string))
		{
			return $bytes;
		}

		$string = gzinflate($string);

		for ($i = 0; $i < strlen($string); $i++)
		{
			$chr = $string[$i];
			$str_byte = Board::padByteByLeft(decbin(ord($string[$i])), 8);
			$bytes[] = bindec(substr($str_byte, 0, 4));
			$right_byte = bindec(substr($str_byte, 4));
			if ($right_byte < bindec(Board::NO_MORE_TILE))
			{
				$bytes [] = $right_byte;
			}
		}

		return $bytes;
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

		$values = Board::binStringToArray($binary_data);
		foreach ($values as $tile_value)
		{
			$this->tiles[] = new Tile($tile_value);
		}
	}

	public function getHtmlTile($offset)
	{
		$offset = intval($offset);
		$tile = $this->tiles[$offset];
		if (get_class($tile) !== 'Tile')
		{
			throw new Exception('Invalid offset value');
		}

		$id = 'tile_'.$offset;

		$tile_state = $this->tiles[$offset]->getState();
		if ($tile_state === Tile::PUB_EMPTY)
		{
			$tile_state += $this->getMinesAround($offset);
		}

		switch ($tile_state)
		{
		case 1: $icon = 'up'; break;
		case 2: $icon = 'up-flag'; break;
		case 3: $icon = 'up-question'; break;
		case 4: $icon = 'bomb'; break;
		case 10: $icon = 'empty'; break;
		case 11: $icon = '1'; break;
		case 12: $icon = '2'; break;
		case 13: $icon = '3'; break;
		case 14: $icon = '4'; break;
		case 15: $icon = '5'; break;
		case 16: $icon = '6'; break;
		case 17: $icon = '7'; break;
		case 18: $icon = '8'; break;
		default:
			throw new Exception('Unknown tile state');
		}
		$src = '/images/'.$icon.'.png';

		$class = '';
		if ($tile_state >= 1 && $tile_state <= 3)
		{
			$class = 'clickable';
		}

		$html = '<img id="'.$id.'" class="'.$class.'" src="'.$src.'" />';

		return $html;
	}

	public function getHtmlStatus()
	{
		$class = 'grey';
		$status = '--';

		if ($this->isWon())
		{
			$class = 'green';
			$status = 'Game won!';
		}
		elseif ($this->isLost())
		{
			$class = 'red';
			$status = 'Game lost!';
		}

		$html = '<span class="'.$class.'">'.$status.'</span>';

		return $html;
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
		$bytes = array();
		foreach ($this->tiles as $tile)
		{
			$bytes[] = $tile->getValue();
		}
		
		$binString = Board::arrayToBinString($bytes);

		return $binString;
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
