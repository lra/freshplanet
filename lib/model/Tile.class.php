<?php

class Tile
{
	// The format of a tile is 3 bits long:
	// MAA eg: 000, 111, 101

	// Is the tile mined (M)
	const STATE_EMPTY = 0;
	const STATE_MINED = 4;

	// User actions (AA)
	const STATE_UNTOUCHED  = 0;
	const STATE_FLAGGED    = 1;
	const STATE_QUESTIONED = 2;
	const STATE_REVEALED   = 3;

	// Default value of a tile
	const STATE_DEFAULT = 0;

	private $value;

	/**
	 * Private functions
	 */

	private function setValue($value)
	{
		if ($value < 0 || $value > (Tile::STATE_MINED | Tile::STATE_REVEALED))
		{
			throw new Exception ('Value out of range');
		}

		$this->value = $value;
	}

	/**
	 * Public functions
	 */

	public function __construct($value = Tile::STATE_DEFAULT)
	{
		$this->setValue($value);
	}

	public function getValue()
	{
		return $this->value;
	}

	// Mine bit

	public function setMined()
	{
		$this->setValue($this->getValue() | Tile::STATE_MINED);
	}

	public function isMined()
	{
		return (($this->value & Tile::STATE_MINED) > 0);
	}

	// Untouched bit

	public function setUntouched()
	{
		$this->setValue($this->getValue() & Tile::STATE_MINED);
	}

	public function isUntouched()
	{
		return (($this->getValue() & Tile::STATE_REVEALED) === Tile::STATE_UNTOUCHED);
	}

	// Flagged bit

	public function setFlagged()
	{
		$this->setValue(($this->getValue() & Tile::STATE_MINED) | Tile::STATE_FLAGGED);
	}

	public function isFlagged()
	{
		return (($this->getValue() & Tile::STATE_REVEALED) === Tile::STATE_FLAGGED);
	}

	// Questioned bit

	public function setQuestioned()
	{
		$this->setValue(($this->getValue() & Tile::STATE_MINED) | Tile::STATE_QUESTIONED);
	}

	public function isQuestioned()
	{
		return (($this->getValue() & Tile::STATE_REVEALED) === Tile::STATE_QUESTIONED);
	}

	// Revealed bit

	public function setRevealed()
	{
		$this->setValue(($this->getValue() & Tile::STATE_MINED) | Tile::STATE_REVEALED);
	}

	public function isRevealed()
	{
		return (($this->getValue() & Tile::STATE_REVEALED) === Tile::STATE_REVEALED);
	}
}
