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
	
	public function scoring($post) {
		if(isset($post->votes['score'])) {
			return $post->votes['score'];
		}
		return 0;
	}
	
	public function postStub($post) {
		$author = $post->tags->getTag("author")?:$post->tags->getTag("source");
		
		$parent = $post->_parent;
		switch($post->_type) {
			case 'answer':
			case 'question-comment':
				while($parent->_parent->_id) {
					$parent = $parent->_parent;
				}
				if($parent->export() == array()) return null;
				$post = $parent;
				break;
		}
		return $this->htmlTag("div", array("class" => "post-stub rounded center-shadow"),
			// $this->htmlTag("div", array("class" => "inline-flow"), ">")."". // Minimize / Maximize
			$this->htmlTag("div", array("class" => "stub-score rounded text-verylarge ".$this->color($this->scoring($post))), $this->scoring($post))."".
			$this->htmlTag("div", array("class" => "stub-title rounded text-large center-shadow"), $this->view->postLink($post))."".
				$this->htmlTag("div", array("class" => "stub-meta inline-flow font-sans"), 
					$this->htmlTag("span", array(), $this->view->profileLink($author))."".
					$this->htmlTag("span", array(), " ".$this->whatsThis($post))."".
					$this->htmlTag("span", array(), " ".$this->toWhat($post))."".
					$this->htmlTag("span", array(), " ○ ".$this->view->timeAgo($post->_created))
				)			
		);
	}
	
	public function color($value) {
		if((int)$value >= 50) {
			return " tc-legendary tc-shadow";
		}
		if((int)$value >= 25) {
			return " tc-epic tc-shadow";
		}
		if((int)$value >= 10) {
			return " tc-rare tc-shadow";
		}
		if((int)$value >= 5) {
			return " tc-uncommon tc-shadow";
		}
		if((int)$value < 0) {
			return " tc-poor";
		}
		return " tc-common";
	}
} // END class EpicDb_View_Helper_PostStub