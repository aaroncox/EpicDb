<?php
class EpicDb_Form extends MW_Form {
	protected $_description = "Empty Description";
	protected $_title = "Empty Title";

	public function setDescription($description) {
		$this->_description = $description;
		return $this;
	}

	public function getDescription() {
		return $this->_description;
	}

	public function setTitle($title) {
		$this->_title = $title;
		return $this;
	}

	public function getTitle() {
		return $this->_title;
	}
	
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