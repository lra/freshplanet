<?php
class LoginForm extends BaseForm
{
  public function configure()
  {
    $this->setWidgets(array('email'   => new sfWidgetFormInputText()));
  }
}
