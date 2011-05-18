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

	public function commentAction() {
		$parent = $this->view->parent = $this->getPost();
		$newComment = EpicDb_Mongo::db('comment');
		$newComment->_parent = $parent;
		$commentForm = $this->view->form = $newComment->getEditForm();
		$this->_handleMWForm($commentForm, 'comment');
	}
	
	public function answerAction() {
		$query = array(
			'id' => (int) $this->getRequest()->getParam('id')
		);
		$question = $this->view->post = EpicDb_Mongo::db('question')->fetchOne($query);
		$this->view->hideComments = true;
		if($this->_helper->auth->getUserProfile()) {
			$newAnswer = EpicDb_Mongo::db('answer');
			$newAnswer->_parent = $question;
			$answerForm = $this->view->form = $newAnswer->getEditForm();
			$this->_handleMWForm($answerForm, 'answer');
		}
	}

	public function questionsAction() {

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

				$this->view->headLink()->appendAlternate(
					$this->view->url(array(
						'tagged' => $request->getParam('tagged')
					),'se_feeds_tag',true),
					"application/rss+xml",
					$this->view->title
				);
			} else {
				$this->view->title = "Recent Questions";
				$this->view->headLink()->appendAlternate(
					$this->view->url(array(),'se_feeds',true),
					"application/rss+xml",
					$this->view->title
				);
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
			
			$questions = EpicDb_Mongo::db('question')->fetchAll($query, $sort);
			$paginator = Zend_Paginator::factory($questions);
			$paginator->setCurrentPageNumber($this->getRequest()->getParam('page', 1));

			$this->view->questions = $paginator;

			$this->view->popularTags = array();// EpicDb_Mongo_Post::getTagsByUsage();
		}
	}
	public function postJson() {
		if($this->_request->isXmlHttpRequest()) {
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
						'text' => 'Read',
						'tooltip' => 'Read the full version on R2-Db.com',
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
			
			if($post instanceOf EpicDb_Mongo_Post_Article_Rss) {	
				$site = $post->tags->getTag('source');
				$controls['parentLink'] = (string) $this->view->button(
					array(
					), null, true,
					array(
						'url' => $post->link,
						'icon' => 'pencil',
						'text' => 'View Original',
						'style' => 'float: right',
						'tooltip' => 'Head on over to '.$site->name.' to check out the full article!',
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

				if($post instanceOf EpicDb_Mongo_Post) {
					$controls['commentLink'] = (string) $this->view->button(
						array(
							'post' => ($post instanceOf EpicDb_Mongo_Post_Question_Comment) ? $post->_parent : $post, // You can't comment on a comment currently, so let's comment on what they commented on.
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
	}

} // END class EpicDb_Post_Controller_Abstract