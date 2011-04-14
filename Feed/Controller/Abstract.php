<?php
/**
 * EpicDb_Feed_Controller_Abstract
 *
 * Abstract controller for Feeds 
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
abstract class EpicDb_Feed_Controller_Abstract extends MW_Controller_Action
{
	protected $_profile = null;
	public function init() {
		$this->getProfile();
	}
	public function getFeed() {
		return $this->view->feed = EpicDb_Mongo::db('post')->getProfileFeed($this->_profile);
	}
	public function getProfile() {
		return $this->view->profile = $this->_profile = EpicDb_Auth::getInstance()->getUserProfile();
	}
	public function indexAction() {
		$feed = $this->getFeed();
	}
}