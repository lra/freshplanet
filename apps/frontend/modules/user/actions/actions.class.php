<?php

/**
 * user actions.
 *
 * @package    minesweeper
 * @subpackage user
 * @author     Laurent Raufaste
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class userActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
		if ($this->getUser()->isAuthenticated())
		{
			$this->redirect('user/loginSuccessful');
		}

		$this->getUser()->setFlash('notice', 'Glop !');

		$this->loginForm = new LoginForm();
		$this->registerForm = new RegisterForm();
  }

 /**
  * Executes login action
  *
  * @param sfRequest $request A request object
  */
  public function executeLogin(sfWebRequest $request)
  {
	}

 /**
  * Executes register action
  *
  * @param sfRequest $request A request object
  */
  public function executeRegister(sfWebRequest $request)
  {
	}

 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeLoginSuccessful(sfWebRequest $request)
  {
	}
}
