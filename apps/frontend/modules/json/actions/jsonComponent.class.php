<?php

class jsonComponent extends sfComponent
{
  function execute($request)
  {
    if (sfConfig::get('sf_debug') && !$this->getRequest()->isXmlHttpRequest())
    {
      $this->setVar('data', $this->data);
    }
    else
    {
      $this->getResponse()->setHttpHeader('Content-type', 'application/json');
      $this->getResponse()->setContent(json_encode($this->data));
      return sfView::NONE;
    }
  }
}
