<?php

class Board
{
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

		if ($boardsize < sfConfig::get('app_board_minsize')
				|| $boardsize > sfConfig::get('app_board_maxsize'))
		{
			throw new Exception('Invalid board size');
		}

		$binary_data = '';

		for ($i = 0; $i < $boardsize; $i++)
		{
			$tile = new Tile();
			if (sfConfig::get('app_board_minepercentage') >= rand(1, 100))
			{
				$tile->setMined();
			}
			$binary_data .= pack('C', $tile->getValue());
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

		$tiles_data = unpack('C*', $binary_data);

		foreach ($tiles_data as $tile_value)
		{
			$this->tiles[] = new Tile($tile_value);
		}
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
			$binary_data .= pack('C', $tile->getValue());
		}

		return $binary_data;
	}
	
	public function leftClick($offset)
	{
		if (!isset($this->tiles[$offset]))
		{
			throw new Exception('Tile not found');
		}
	
		if ($this->tiles[$offset]->isUntouched())
		{
			$this->tiles[$offset]->setRevealed();			
		}
	}
}
