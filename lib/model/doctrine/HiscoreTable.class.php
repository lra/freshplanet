<?php


class HiscoreTable extends Doctrine_Table
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Hiscore');
    }

		public function getAllOrderedByBoardwidthAndTime()
		{
			$q = $this->createQuery('h')
				->leftJoin('h.User u')
				->orderBy('h.time ASC')
				->orderBy('h.boardwidth DESC');

			return $q->execute();
		}
}