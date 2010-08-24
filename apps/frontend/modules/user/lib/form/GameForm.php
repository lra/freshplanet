<?php
class GameForm extends BaseForm
{
	public function configure()
	{
		$valid_widths = array();
		for ($size = sfConfig::get('app_board_minwidth');
			$size <= sfConfig::get('app_board_maxwidth');
			$size++)
			{
				$valid_widths[$size] = $size;
			}
		$this->setWidgets(array('width' => new sfWidgetFormSelect(array('choices' => $valid_widths))));
	}
}
