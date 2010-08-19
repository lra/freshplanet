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
		// If the user is already authenticated, no need to display the login forms
		if ($this->getUser()->isAuthenticated())
		{
			$this->redirect('user/loginSuccessful');
		}

		// The login forms
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
		// This action should only be accessed by a login form
		$this->forward404Unless($request->isMethod('post'));

		$email = $request->getParameter('email');
		$user = $this->getUser();

		// No email ?
		if (!$email)
		{
			$user->setFlash('error', 'An email is required');
			$this->redirect('user/index');
		}

		if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$user->setFlash('error', 'Invalid Email entered');
			$this->redirect('user/index');
		}
		
		// Try to find the user
		$dbUser = Doctrine_Core::getTable('User')->findOneByEmail($email);

		// The user logged in, authenticate him
		if ($dbUser)
		{
			$user->setAttribute('id', $dbUser->getId());
			$user->setAttribute('name', $dbUser->getName());
			$user->setFlash('notice', 'Welcome '.$dbUser->getFirstname());
			$user->setAuthenticated(true);
			// Shows the user that he logged in
			$this->redirect('user/loginSuccessful');
		}
		else
		{
			$user->setFlash('error', 'Unable to find a user with the email '.$email);
			$this->redirect('user/index');
		}
	}

	/**
	* Executes register action
	*
	* @param sfRequest $request A request object
	*/
	public function executeRegister(sfWebRequest $request)
	{
		// This action should only be accessed by a register form
		$this->forward404Unless($request->isMethod('post'));
		
		$email = $request->getParameter('email');
		$firstname = $request->getParameter('firstname');
		$lastname = $request->getParameter('lastname');
		
		$user = $this->getUser();
		
		// Missing some data ?
		if (!($email && $firstname && $lastname))
		{
			$user->setFlash('error', 'Every field is mandatory');
			$this->redirect('user/index');
		}
		
		if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$user->setFlash('error', 'Invalid Email entered');
			$this->redirect('user/index');
		}
		
		$dbUser = new User();
		$dbUser->setEmail($email);
		$dbUser->setFirstname($firstname);
		$dbUser->setLastname($lastname);
		$dbUser->save();

		// The user logged in, authenticate him
		if (!$dbUser->isNew())
		{
			$user->setAttribute('id', $dbUser->getId());
			$user->setAttribute('name', $dbUser->getName());
			$user->setFlash('notice', 'Welcome '.$dbUser->getFirstname());
			$user->setAuthenticated(true);
			// Shows the user that he logged in
			$this->redirect('user/loginSuccessful');
		}
		else
		{
			$user->setFlash('error', 'Unable to find a user with the email '.$email);
			$this->redirect('user/index');
		}
	}

 /**
  * Executes loginSuccessful action
  *
  * @param sfRequest $request A request object
  */
  public function executeLoginSuccessful(sfWebRequest $request)
  {
		$user = $this->getUser();
		$this->dbUser = Doctrine_Core::getTable('User')->find($user->getAttribute('id'));
	}

 /**
  * Executes logout action
  *
  * @param sfRequest $request A request object
  */
  public function executeLogout(sfWebRequest $request)
  {
		$this->getUser()->getAttributeHolder()->remove('id');
		$this->getUser()->getAttributeHolder()->remove('name');
		$this->getUser()->setAuthenticated(false);

		$this->getUser()->setFlash('notice', 'You have successfully logged out');

		// Shows the user that he logged in
		$this->redirect('user/index');
	}
}
