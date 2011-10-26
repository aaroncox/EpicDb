<?php
/**
 * EpicDb_Post_Controller_Abstract
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Post_Controller_Abstract extends MW_Controller_Action
{

	public function init()
	{
		parent::init();
		$contextSwitch = $this->_helper->getHelper('ContextSwitch');
		if (!$contextSwitch->hasContext('rss')) {
			$contextSwitch->addContext('rss', array(
				'callbacks' => array(
						'init' => array($this, 'initRssContext'),
						'post' => array($this, 'postRssContext'),
				)
			));
		}
		if (!$contextSwitch->hasContext('stub')) {
			$contextSwitch->addContext('stub', array(
				'callbacks' => array(
						'init' => array($this, 'initStubContext'),
						'post' => array($this, 'postStubContext'),
				)
			));
		}
		if (!$contextSwitch->hasContext('poll')) {
			$contextSwitch->addContext('poll', array(
				'callbacks' => array(
						'init' => array($this, 'initPollContext'),
						'post' => array($this, 'postPollContext'),
				)
			));
		}
		$contextSwitch->addActionContext('questions', 'rss');
		$contextSwitch->addActionContext('view', 'rss');
		$contextSwitch->addActionContext('view', 'stub');
		$contextSwitch->addActionContext('view', 'poll');
		try {
			$contextSwitch->initContext();
		} catch (Exception $e) {
			// Unknown Context Exception?
		}
	}
	
	public function initStubContext() {
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$view = $viewRenderer->view;
		if ($view instanceof Zend_View_Interface) {
				$viewRenderer->setNoRender(true);
		}		
	}
	
	public function postStubContext() {
		$this->getPost();
		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		$post = $this->view->post;
		
		// Prob a better way to do this!
		$parent = $post->_parent;
		while($parent->_parent->_id) {
			$parent = $parent->_parent;
		}
		
		
		
		
		// Default Controls for every post...
		$controls = array(
			'permaLink' => (string) $this->view->button(
				array(
					'post' => $this->view->post, 
					'action' => 'view'
				), 'post', true,
				array(
					'icon' => 'comment',
					'text' => 'Read w/ '.$this->view->post->findComments()->count().' comments',
					'tooltip' => 'Read the full version including comments',
				)
			),
			'parentLink' => (string) $this->view->button(
				array(
					'post' => ($parent->export() != array()) ? $parent : $this->view->post,
					'action' => 'view',
				), 'post', true,
				array(
					'icon' => 'pencil',
					'text' => ($parent->export() != array()) ? 'View Full Discussion' : 'View Original',
					'style' => 'float: right'
				)
			),
		);

		if($post instanceOf EpicDb_Mongo_Post_Article && $post->link) {	
			$site = $post->tags->getTag('source');
			$controls['parentLink'] = (string) $this->view->button(
				array(
				), null, true,
				array(
					'url' => $post->link,
					'icon' => 'link',
					'text' => 'View Original',
					'style' => 'float: right',
					'tooltip' => 'Head on over to '.$site->name.' to view the original!',
				)
			);
		}

		if($post instanceOf EpicDb_Mongo_Post_Article_Rss) {	
			$site = $post->tags->getTag('source');
			$controls['parentLink'] = (string) $this->view->button(
				array(
				), null, true,
				array(
					'url' => $post->link,
					'icon' => 'link',
					'text' => 'View Original',
					'style' => 'float: right',
					'tooltip' => 'Head on over to '.$site->name.' to view the original!',
				)
			);
		}
		
		if(EpicDb_Auth::getInstance()->getUser()) {
			if($post->_type == 'question') {
				$controls['answerLink'] = (string) $this->view->button(
					array(
						'post' => $post,
						'action' => 'answer',
					), 'post', true,
					array(
						'icon' => 'pencil',
						'text' => 'Answer',
						'tooltip' => 'Post an answer to this question.',
					)
				);
			}

			$target = $post;
			while($target->_parent->id && $target->_type != "answer") {
				$target = $target->_parent;
			}
			if($post instanceOf EpicDb_Mongo_Post_Message) {
				$controls['replyLink'] = (string) $this->view->button(
					array(
						'post' => $target,
						'action' => 'reply',
					), 'post', true,
					array(
						'icon' => 'pencil',
						'text' => 'Reply',
						'tooltip' => 'Reply to this message.',
						'style' => 'float: left'
					)
				);				
			} else {
				$controls['commentLink'] = (string) $this->view->button(
					array(
						'post' => $target,
						'action' => 'comment',
					), 'post', true,
					array(
						'icon' => 'pencil',
						'text' => 'Comment',
						'tooltip' => 'Post a comment on this post.',
					)
				);				
			}				
		}
		
		if(EpicDb_Auth::getInstance()->hasPrivilege(new EpicDb_Auth_Resource_Moderator)) {
			$controls['_delete'] = $this->view->button(array(
				'action'=>'delete',
				'post'=>$target,
			), 'post', true, array(
				'icon' => 'trash',
				'tooltip' => 'Delete this Post',
			));				
		}
		
		
		ksort($controls);
		// var_dump($controls); exit;
		$result = array(
			'post' => $post->id,
			'postType' => $post->_type,
			'body' => $post->body,
			'controls' => implode($controls),
		);
		echo Zend_Json::encode($result);
		exit;
	}

	public function initPollContext()
	{
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$view = $viewRenderer->view;
		if ($view instanceof Zend_View_Interface) {
				$viewRenderer->setNoRender(true);
		}
	}
	
	public function postPollContext()
	{
		if($user = EpicDb_Auth::getInstance()->getUserProfile()) {
			$poll = $this->getPost();
			$raw = $this->getRequest()->getParam("choices");
			$choices = explode("|", $raw);
			foreach($poll->options as $option) {
				$option->voters->untag($user);					
			}
			foreach($choices as $choice) {	
				$parts = explode("-", $choice);
				if(isset($parts[2])) {
					$id = $parts[2];
					foreach($poll->options as $option) {
						if($option->id == $id) {
							$option->voters->tag($user);
						}
					}
				}
			}
			$poll->save();
		}
		exit;
	}

	public function initRssContext()
	{
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$view = $viewRenderer->view;
		if ($view instanceof Zend_View_Interface) {
				$viewRenderer->setNoRender(true);
		}
	}

	protected function _rssTitle()
	{
		return strip_tags($this->view->title) . " :: EpicDb RSS";
	}

	public function postRssContext()
	{
		$view = $this->view;
		$generator = new EpicDb_Feed_Generator(array('view'=>$view));
		$generator->setLink($view->url());
		$generator->setTitle( $this->_rssTitle() );
		if ( $parent = $this->view->question ?: $this->view->post ) {
			$generator->addPost($parent);
			$answers = $parent->findResponses( false );
			foreach ($answers as $post) $generator->addPost($post);

		} else {
			foreach ($this->view->questions as $post) $generator->addPost($post);
		}
		$generator->toRss()->send();
	}
	
	/**
	 * getPost - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function getPost()
	{
		$this->view->post = $post = $this->_request->getParam('post');
		if(!$post) {
			throw new Exception("Unable to load post...");
		}
		return $post;
	}
	
	public function editAction() {
		$post = $this->getPost();
		MW_Auth::getInstance()->requirePrivilege($post, 'edit');
		$revision = $this->getRequest()->getParam("rev", false);
		$this->view->form = $form = $post->getEditForm(array("rev" => $revision));
		$this->_handleMWForm($form);
	}

	public function getCommentForm($parent) {
		if($parent instanceOf EpicDb_Mongo_Post_Question) {
			$newComment = EpicDb_Mongo::db('question-comment');			
		} else {
			$newComment = EpicDb_Mongo::db('comment');
		}
		$newComment->_parent = $parent;
		$newComment->tags->tag($parent, 'parent');
		$commentForm = $newComment->getEditForm();
		return $commentForm;
	}
	
	public function commentAction() {
		$parent = $this->view->parent = $this->getPost();
		$commentForm = $this->view->form = $this->getCommentForm($parent);
		$this->_handleMWForm($commentForm, 'comment');
	}
	
	public function replyAction() {
		$parent = $this->view->parent = $this->getPost();
		$newReply = EpicDb_Mongo::newDoc('message');
		$newReply->_parent = $parent;
		$newReply->tags->tag($parent, 'parent');
		$replyForm = $this->view->form = $newReply->getEditForm();
		$this->_handleMWForm($replyForm, 'comment');
	}
	
	public function answerAction() {
		$query = array(
			'id' => (int) $this->getRequest()->getParam('id')
		);
		$question = $this->view->post = EpicDb_Mongo::db('question')->fetchOne($query);
		$this->view->hideComments = true;
		$newAnswer = EpicDb_Mongo::db('answer');
		$newAnswer->_parent = $question;
		$newAnswer->tags->tag($question, 'parent');
		$answerForm = $this->view->form = $newAnswer->getEditForm();
		$this->_handleMWForm($answerForm, 'answer');
	}
	
	protected function _formRedirect($form, $key, $ajax) {
		if($referrer = $form->getElement('referrer')->getValue()) {
			$this->_redirect($referrer);
		}
		$post = $form->getPost();
		if($post->_parent->hasId() && $post->_parent->_parent->hasId() && $parentParent = $post->_parent->_parent) $this->_redirect($this->view->url(array(
			'post'=>$parentParent,
			'action'=>'view',
		), 'post', true));
		if($post->_parent->hasId() && $parent = $post->_parent) $this->_redirect($this->view->url(array(
			'post'=>$parent,
			'action'=>'view',
		), 'post', true));
		if($post = $form->getPost()) $this->_redirect($this->view->url(array(
			'post'=>$post,
			'action'=>'view',
		), 'post', true)); 
		parent::_formRedirect($form, $key, $ajax);
	}
	

	public function questionsAction() {
		$this->view->breadcrumb = "Questions";
		$this->view->profile = $this->_helper->auth->getUserProfile();

		$request = $this->getRequest();
		$auth = $this->_helper->auth;

		// var_dump($auth->getUser()->roles[0]->export());

		if ($request->getParam('ask')) {
			if(!$auth->getUser()) {
				throw new MW_Auth_Exception("You must be logged in to post a question.");
			}
			$question = EpicDb_Mongo::newDoc('question');
			$this->view->form = $form = $question->getEditForm();
			$this->_helper->viewRenderer('ask');
			$this->_handleMWForm($form, 'ask');

		} else if($question = $request->getParam('id')) {
			$query = array(
				'id' => (int) $this->getRequest()->getParam('id')
			);
			$question = $this->view->question = EpicDb_Mongo::db('question')->fetchOne($query);
			if (!$question) {
				$answer = EpicDb_Mongo::db('answer')->fetchOne($query);
				if (!$answer) throw new MW_Controller_404Exeption('Invalid Question ID');
				$question = $answer->_parent;
				$slug = new MW_Filter_Slug();
				return $this->_redirect($this->view->url(array(
					'id' => $question->id,
					'slug' => $slug->filter($question->title),
					'answer' => $answer->id,
				)).'#answer-'.$answer->id);
			}
			$this->_post = $question;
			$this->getRequest()->setParam('post', $question);
			if (!$this->getRequest()->getParam("format")) {
				$this->_forward('view');
			}
		} else {
			$query = array();
			if($this->view->tag = $tag = $request->getParam("tagged")) {
				$queryData = EpicDb_Search::getInstance()->parseQueryString($tag);
				$query = $queryData['query'];
				$tags = $queryData['terms']['tagged'];
				$query["_type"] = "question";
				$tagLinks = array();
				foreach ($tags as $tag) {
					if ($tag instanceOf EpicDb_Mongo_Record) {
						$tagLinks[] = $this->view->recordLink($tag)."";
					} else {
						$tagLinks[] = $this->view->profileLink($tag)."";
					}
				}
				$this->view->title = "Recent Questions Tagged ".implode(", ", $tagLinks);
			} else {
				$this->view->title = "Recent Questions";
			}
			$this->view->sortBy = $sortBy = $this->getRequest()->getParam("sort");
			switch($sortBy) {
				case "highest-voted":
					$sort = array('votes.score' => -1);
					break;
				case "newest":
					$sort = array('_created' => -1);
					break;
				default:
					$sort = array('touched' => -1, '_created' => -1);
					break;
			}
			$this->view->filterBy = $filterBy = $this->getRequest()->getParam("filter");
			switch($filterBy) {
				case "unanswered":
					$query['$or'][]['_answerCount'] = array('$exists' => false);
					$query['$or'][]['_answerCount'] = 0;
					break;
				case "today":
					$query['_created'] = array('$gt' => time() - (60*60*24));
					break;
				case "72h":
					$query['_created'] = array('$gt' => time() - (60*60*24*3));
					break;
				case "week":
					$query['_created'] = array('$gt' =>time() - (60*60*24*7));
					break;
				case "month":
					$query['_created'] = array('$gt' =>time() - (60*60*24*30));
					break;
				default:
					break;
			}
			Zend_Paginator::setDefaultItemCountPerPage( 10 );
			$questions = EpicDb_Mongo::db('question')->fetchAll($query, $sort);
			$paginator = Zend_Paginator::factory($questions);
			$paginator->setCurrentPageNumber($this->getRequest()->getParam('page', 1));

			$this->view->questions = $paginator;

			$this->view->popularTags = array();// EpicDb_Mongo_Post::getTagsByUsage();
		}
	}
	
	public function pollsAction() {
		$this->view->breadcrumb = "Polls";
		$this->view->profile = $this->_helper->auth->getUserProfile();

		$request = $this->getRequest();
		$auth = $this->_helper->auth;

		if ($request->getParam('create')) {
			if(!$auth->getUser()) {
				throw new MW_Auth_Exception("You must be logged in to post a question.");
			}
			$poll = EpicDb_Mongo::newDoc('poll');
			$this->view->form = $form = $poll->getEditForm();
			$this->_helper->viewRenderer('create-poll');
			$this->_handleMWForm($form, 'create-poll');
		} else if($poll = $request->getParam('id')) {
			$query = array(
				'id' => (int) $this->getRequest()->getParam('id')
			);
			$poll = $this->view->poll = EpicDb_Mongo::db('poll')->fetchOne($query);
			$this->_post = $poll;
			$this->getRequest()->setParam('post', $poll);
			if (!$this->getRequest()->getParam("format")) {
				$this->_forward('view');
			}
		} else {
			$query = array();
			if($this->view->tag = $tag = $request->getParam("tagged")) {
				$queryData = EpicDb_Search::getInstance()->parseQueryString($tag);
				$query = $queryData['query'];
				$tags = $queryData['terms']['tagged'];
				$query["_type"] = "poll";
				$tagLinks = array();
				foreach ($tags as $tag) {
					if ($tag instanceOf EpicDb_Mongo_Record) {
						$tagLinks[] = $this->view->recordLink($tag)."";
					} else {
						$tagLinks[] = $this->view->profileLink($tag)."";
					}
				}
				$this->view->title = "Recent Polls Tagged ".implode(", ", $tagLinks);
			} else {
				$this->view->title = "Recent Polls";
			}
			switch($this->getRequest()->getParam("sort")) {
				case "highest-voted":
					$sort = array('votes.score' => -1, '_created' => -1);
					break;
				case "lowest-voted":
					$sort = array('votes.score' => 1, '_created' => -1);
					break;
				case "oldest":
					$sort = array('_created' => 1);
					break;
				case "newest":
				default:
					$sort = array('touched' => -1, '_created' => -1);
					break;
			}
			Zend_Paginator::setDefaultItemCountPerPage( 10 );
			$polls = EpicDb_Mongo::db('poll')->fetchAll($query, $sort);
			$paginator = Zend_Paginator::factory($polls);
			$paginator->setCurrentPageNumber($this->getRequest()->getParam('page', 1));

			$this->view->polls = $paginator;

			$this->view->popularTags = array();// EpicDb_Mongo_Post::getTagsByUsage();
		}
	}
	
	public function sourceAction() {
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
	  $post = $this->getPost();
	  $rev = $this->getRequest()->getParam("rev");
	  if(strlen($rev)) {
	    $rev = (int) $rev;
	    $source = $post->revisions[$rev]->source;
	  } else {
	    $source = $post->source;
	  }
		if(!$source && $post instanceOf EpicDb_Mongo_Post_Article_RSS) {
			$source = htmlspecialchars($post->body);
		}
    $this->getResponse()->setHeader("Content-type", "text/plain");
    echo "<pre>".$source."</pre>";
		exit;
	}
	public function revisionsAction() {
	  $this->getPost();
		$this->view->breadcrumb = "Revision History";
	}
	
	/**
	 * viewAction - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function viewAction() {
		// we should probably just call this "post" in the view too...
		$this->view->post = $post = $this->getPost();
		$this->_helper->auth->requirePrivilege($post, 'view');
		$params = $this->getRequest()->getParams();

		while($post->_parent->id) {
			$this->view->post = $post = $post->_parent;
		}
		
		// var_dump($post->tags->getTag('author')->export()); exit;
		if ( $post instanceOf EpicDb_Mongo_Post_Question ) {
			$slug = new MW_Filter_Slug();
			$this->view->headLink()->append((object)array(
				'rel' => 'canonical',
				'href' => $this->view->url(array(
					'post' => $post,
					),"questions", true)
			));
		} else {
			$this->view->headLink()->append((object)array(
				'rel' => 'canonical',
				'href' => $this->view->url(array(
						'post' => $post,
					),"post", true)
			));
		}
		if($post instanceOf EpicDb_Mongo_Post_Question ) {
			$newAnswer = EpicDb_Mongo::db('answer');
			$newAnswer->_parent = $post;
			$answerForm = $this->view->answerForm = $newAnswer->getEditForm();
			$this->_handleMWForm($answerForm, 'answer');
		}
		if($post instanceOf EpicDb_Mongo_Post_Message ) {
			$paginator = Zend_Paginator::factory($post->findResponses());
			$paginator->setCurrentPageNumber($this->getRequest()->getParam('page', 1));
			$this->view->responses = $paginator;
			$newReply = EpicDb_Mongo::newDoc('message');
			$newReply->_parent = $post;
			$newReply->tags->tag($post, 'parent');
 			$this->view->form = $replyForm = $newReply->getEditForm();
			$this->_handleMWForm($replyForm, 'reply');
		}
	}
	

} // END class EpicDb_Post_Controller_Abstract