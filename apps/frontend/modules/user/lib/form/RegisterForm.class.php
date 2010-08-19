<?php
class RegisterForm extends BaseForm
{
  public function configure()
  {
    $this->setWidgets(array(	'email'     => new sfWidgetFormInputText(),
								'firstname' => new sfWidgetFormInputText(),
								'lastname'  => new sfWidgetFormInputText()));
	$this->setValidators(array(	'email'		=> new sfValidatorEmail(),
								'firstname'	=> new sfValidatorString(),
								'lastname'	=> new sfValidatorString()));
  }
}
