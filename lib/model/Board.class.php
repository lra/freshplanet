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
	
	public function leftClick($offset)
	{
		if (!isset($this->tiles[$offset]))
		{
			throw new Exception('Tile not found');
		}
	
		$return = Board::GAME_ERROR;

		$tile = $this->tiles[$offset];
		if (!$tile->isRevealed())
		{
			$tile->setRevealed();			
			$return = Board::GAME_DISCOVERED;
		}

		return $return;
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
				$return = Board::GAME_FLAGGED;
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
