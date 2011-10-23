<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Wiki_Controller_Abstract extends MW_Controller_Action
{
	protected $_wiki = null;
	protected $_record = null;
	public function getWiki() {
		if($id = $this->getRequest()->getParam("record")) {
			$record = $this->_record = EpicDb_Mongo::db('record')->find(new MongoId($id));
			if(!$record) {
				$record = $this->_record = EpicDb_Mongo::db('profile')->find(new MongoId($id));				
			}
			if(!$type = $this->getRequest()->getParam("type")) {
				$type = "new";
			}
			return $this->view->wiki = $this->_wiki = EpicDb_Mongo::db('wiki')->get($record, $type);
		} else {
			return EpicDb_Mongo::newDoc('wiki');
		}
	}
	public function indexAction() {
		if(MW_Auth::getInstance()->getUser()) {
			if(EpicDb_Mongo::db('group')->fetchOne(array("id" => 10))->isMember() || MW_Auth::getInstance()->getUser()->isMember(MW_Auth_Group_Super::getInstance())) {		
				$wiki = $this->getWiki();
				$revision = $this->getRequest()->getParam("rev", false);
				if($type = $this->getRequest()->getParam('type')) {
					$form = $this->view->form = new EpicDb_Form_Wiki(array("wiki" => $wiki, "record" => $this->_record, "type" => $type, "rev" => $revision));													
				} else {
					$form = $this->view->form = new EpicDb_Form_Wiki(array("wiki" => $wiki, "record" => $this->_record, "rev" => $revision));								
				}
				$this->_handleMWForm($form, 'wiki');			
			} else {
				return $this->_redirect("/user/login");
			}
		}
	}
	public function revisionsAction() {
		$this->view->wiki = $this->getWiki();
	}
} // END class EpicDb_Wiki_Controller_Abstract extends MW_Controller_Action