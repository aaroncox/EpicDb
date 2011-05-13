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
				'permaLink' => (string) $this->view->button(
					array(
						'post' => $this->view->post, 
						'action' => 'view'
					), 'post', true,
					array(
						'icon' => 'pencil',
						'text' => 'Perma-Link',
					)
				),
			);
			
			if($post instanceOf EpicDb_Mongo_Post_Article_Rss) {	
				$controls['parentLink'] = (string) $this->view->button(
					array(
					), null, true,
					array(
						'url' => $post->link,
						'icon' => 'pencil',
						'text' => 'View Original on '.$post->tags->getTag('source')->name,
						'style' => 'float: right'
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
							'url' => $post->link,
							'icon' => 'pencil',
							'text' => 'Answer this Question',
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
							'url' => $post->link,
							'icon' => 'pencil',
							'text' => 'Leave Comment',
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