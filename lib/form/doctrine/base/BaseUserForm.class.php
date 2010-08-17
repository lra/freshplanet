<?php

/**
 * User form base class.
 *
 * @method User getObject() Returns the current form's model object
 *
 * @package    minesweeper
 * @subpackage form
 * @author     Laurent Raufaste
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseUserForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'email'          => new sfWidgetFormInputText(),
      'firstname'      => new sfWidgetFormInputText(),
      'lastname'       => new sfWidgetFormInputText(),
      'game_board'     => new sfWidgetFormTextarea(),
      'game_time'      => new sfWidgetFormInputText(),
      'last_action_at' => new sfWidgetFormDateTime(),
      'created_at'     => new sfWidgetFormDateTime(),
      'updated_at'     => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'email'          => new sfValidatorEmail(array('max_length' => 255)),
      'firstname'      => new sfValidatorString(array('max_length' => 255)),
      'lastname'       => new sfValidatorString(array('max_length' => 255)),
      'game_board'     => new sfValidatorString(array('required' => false)),
      'game_time'      => new sfValidatorInteger(array('required' => false)),
      'last_action_at' => new sfValidatorDateTime(array('required' => false)),
      'created_at'     => new sfValidatorDateTime(),
      'updated_at'     => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'User', 'column' => array('email')))
    );

    $this->widgetSchema->setNameFormat('user[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'User';
  }

}
