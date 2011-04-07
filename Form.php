<?php
class EpicDb_Form extends MW_Form {
  /**
   * adds the MW_Form prefix path
   *
   * @return void
   * @author Corey Frang
   */
  public function init()
  {
    parent::init();
    $this->addPrefixPath("EpicDb_Form", dirname(__FILE__)."/Form");
  }
}