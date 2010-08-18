<?php
class RegisterForm extends BaseForm
{
  public function configure()
  {
    $this->setWidgets(array('email'     => new sfWidgetFormInputText(),
														'firstname' => new sfWidgetFormInputText(),
														'lastname'  => new sfWidgetFormInputText()));
  }
}
