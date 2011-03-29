<?php
/**
 * EpicDb_Profile_Controller_Abstract
 *
 * Abstract controller for Profiles 
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
abstract class EpicDb_Profile_Controller_Abstract extends MW_Controller_Action
{
	// The EpicDb_Mongo_Profile from the request parameter "profile". 
	protected $_profile;

	public function init() {
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext->addActionContext('feed', 'html')
								->addActionContext('manage', 'html')
								->addActionContext('invite', 'html')
								;
		$contextSwitch = $this->_helper->getHelper('ContextSwitch');
		if (!$contextSwitch->hasContext('rss')) {
  		$contextSwitch->addContext('rss', array(
        'callbacks' => array(
            'init' => array($this, 'initRssContext'),
            'post' => array($this, 'postRssContext'),
        )      
  		));		  
		}
		$contextSwitch->addActionContext('feed', 'rss');
		try {
  		$ajaxContext->initContext();
		} catch (Exception $e) {
		  // Unknown Context Exception?
		}
		try {
  		$contextSwitch->initContext();
		} catch (Exception $e) {
		  // Unknown Context Exception?
		}
		
		parent::init();
	}
	
	public function initRssContext()
	{
	  $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
    $view = $viewRenderer->view;
    if ($view instanceof Zend_View_Interface) {
        $viewRenderer->setNoRender(true);
    }
	}
	
	public function postRssContext()
	{
	  $generator = new MW_Feed_Generator();
	  $generator->setLink($this->view->url());
	  $generator->setTitle($this->getProfile()->name.' '.ucwords($this->view->filter).' RSS :: R2-DB.com :: Star Wars: The Old Republic (SWTOR) Database');
	  foreach ($this->view->posts as $post)
	  {
	    $link = $generator->makeUri($this->view->url(array('post'=>$post), 'post', true));
			if($post->title) {
				$title = strip_tags($post->title);
			} else {
				$title = $post->tldr ?: ucfirst($post->_type).' by '.$post->_profile->name.' on '.$post->tags->getTag('subject')->name;
			}
	    $generator->addEntry(array(
          'title' => $title,
          'link' => $link,
          'guid' => $link,
					'author' => $post->feedAuthor,
          'description' => $this->view->htmlFragment($post->body, 350),
          'content' => $this->view->htmlFragment($post->body, 350),
          'lastUpdate' => $post->_created ?: $post->_updated,
        ));            
	  }
	  $generator->toRss()->send();
	}
	
  public function getProfile()
  {
    if($this->_profile) return $this->_profile;
		if($this->_profile = $this->getRequest()->getParam("profile")) {
			return $this->view->profile = $this->_profile;
		}	
		$id = $this->getRequest()->getParam("id");
		$result = EpicDb_Mongo::db('user')->getProfileById($id);

		if(!$result) {
			throw new MW_Controller_404Exception("Profile not found...");
		}
		return $this->_profile = $this->view->profile = $result;
  }

	
	public function unfollowAction() {
		$this->_helper->auth->unfollow($this->getProfile());
		$this->_redirect($this->getRequest()->getServer('HTTP_REFERER'));
	}


	public function followAction() {
		$this->_helper->auth->follow($this->getProfile());
		$this->_redirect($this->getRequest()->getServer('HTTP_REFERER'));
	}
} // END class EpicDb_Profile_Controller_Abstract