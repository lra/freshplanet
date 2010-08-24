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

	/**
	 * Corresponding icons
	 *
	 * Internal states:
	 * 0: up.png
	 * 1: up-flag.png
	 * 2: up-question.png
	 * 3: empty.png
	 * 4: up.png
	 * 5: up-flag.png
	 * 6: up-question.png
	 * 7: bomb.png
	 */

	/**
	 *
	 * Public states:
	 * 0: up.png (up)
	 * 1: up-flag.png (flag)
	 * 2: up-question.png (question)
	 * 3: empty.png (empty)
	 * 7: bomb.png (bomb)
	 */
	const PUB_UP = 1;
	const PUB_FLAG = 2;
	const PUB_QUESTION = 3;
	const PUB_EMPTY = 4;
	const PUB_BOMB = 5;

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

	public function getState()
	{
		switch ($this->getValue())
		{
		case Tile::STATE_EMPTY|Tile::STATE_UNTOUCHED:
		case Tile::STATE_MINED|Tile::STATE_UNTOUCHED:
			$state = Tile::PUB_UP;
			break;
		case Tile::STATE_EMPTY|Tile::STATE_FLAGGED:
		case Tile::STATE_MINED|Tile::STATE_FLAGGED:
			$state = Tile::PUB_FLAG;
			break;
		case Tile::STATE_EMPTY|Tile::STATE_QUESTIONED:
		case Tile::STATE_MINED|Tile::STATE_QUESTIONED:
			$state = Tile::PUB_QUESTION;
			break;
		case Tile::STATE_EMPTY|Tile::STATE_REVEALED:
			$state = Tile::PUB_EMPTY;
			break;
		case Tile::STATE_MINED|Tile::STATE_REVEALED:
			$state = Tile::PUB_BOMB;
			break;
		default:
			throw new Exception('Unknown value:'.$this->getValue());
		}

		return $state;
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
