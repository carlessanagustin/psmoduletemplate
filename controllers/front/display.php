<?php
class psmoduletemplatedisplayModuleFrontController extends ModuleFrontController
{
  public function initContent()
  {
    parent::initContent();
    $this->setTemplate('display-front.tpl');
  }
}