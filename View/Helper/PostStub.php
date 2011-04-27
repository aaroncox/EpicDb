<?php
/**
 * EpicDb_View_Helper_PostStub
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_PostStub extends MW_View_Helper_HtmlTag
{
	public function toWhat($post) {
		$tags = array();
		foreach($post->tags->getTags('tag') as $tag) {	
			$tags[] = (string)$this->view->recordLink($tag);
		}
		// var_dump($tags); exit;
		if(empty($tags)) return '';
		return " about ".implode(', ', $tags);
	}
	public function whatsThis($post) {
		switch($post->_type) {
			case 'question-comment':
				$type = 'comment';
				break;
			default:
				$type = $post->_type;
				break;
		}
		return " posted a ".$type;
	}
	
	public function postStub($post) {
		$author = $post->tags->getTag("author");
		
		$parent = $post->_parent;
		switch($post->_type) {
			case 'answer':
			case 'question-comment':
				while($parent->_parent->_id) {
					$parent = $parent->_parent;
				}
				break;
		}
		if($parent->export() == array()) return null;
		return $this->htmlTag("div", array("class" => "post-stub rounded center-shadow"),
			// $this->htmlTag("div", array("class" => "inline-flow"), ">")."". // Minimize / Maximize

			$this->htmlTag("div", array("class" => "text-large"), $this->view->postLink($parent?:$post))."".
				$this->htmlTag("div", array("class" => "stub-meta inline-flow font-sans"), 
					$this->htmlTag("span", array(), $this->view->profileLink($author))."".
					$this->htmlTag("span", array(), " ".$this->whatsThis($post))."".
					$this->htmlTag("span", array(), " ".$this->toWhat($parent?:$post))."".
					$this->htmlTag("span", array(), " ".$this->view->timeAgo($post->_created))
				)			
		);
	}
} // END class EpicDb_View_Helper_PostStub