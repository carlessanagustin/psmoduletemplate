<?php
class psmoduletemplatepsmoduletemplateModuleFrontController extends ModuleFrontController
{
  public function initContent()
  {
    parent::initContent();
    $this->setTemplate('psmoduletemplate-front.tpl');
  }
}