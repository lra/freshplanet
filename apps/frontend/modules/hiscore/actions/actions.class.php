<?php

/**
 * hiscore actions.
 *
 * @package    minesweeper
 * @subpackage hiscore
 * @author     Laurent Raufaste
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class hiscoreActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
		$this->hiscores = Doctrine_Core::getTable('Hiscore')->getAllOrderedByBoardwidthAndTime();
  }
}
