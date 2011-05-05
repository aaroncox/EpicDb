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
				$type = ' comment';
				break;
			case 'article-rss':
				$type = 'n Article';
				break;
			case "answer":
				$type = 'n answer';
				break;
			default:
				$type = ' '.$post->_type;
				break;
		}
		return " posted a".$type;
	}
	
	public function scoring($post) {
		if(isset($post->votes['score'])) {
			return $post->votes['score'];
		}
		return 0;
	}
	
	public function voteUrl($post, $vote) {
		return $this->view->url(array(
			'type' => $post->_type,
			'id' => $post->id,
			'vote' => $vote,
		), 'vote-cast', true);
	}
	
	public function stubVote($post) {
		$buttons = "";
		if ($post instanceOf EpicDb_Vote_Interface_Votable) {
			if (!$post instanceOf EpicDb_Vote_Interface_UpOnly) {
				$buttons .= $this->view->voteWidget($post)->makeVoteButton($post, 'down');
			}
			$buttons .= $this->view->voteWidget($post)->makeVoteButton($post, 'up');
			if ($post instanceOf EpicDb_Vote_Interface_Acceptable) {
				$buttons .= $this->view->voteWidget($post)->makeVoteButton($post, 'accept');
			}
		}
		return $buttons;
		
		$score = 0;
		if(isset($post->votes['score'])) $score = $post->votes['score'];
		$vote = null;
		if($profile = EpicDb_Auth::getInstance()->getUserProfile()) {
			$vote = EpicDb_Mongo::db('vote')->getVoteByProfile($post, $profile);			
		} else {
			return ' ';
		}
		// Return the widget
		return 
			$this->htmlTag("a", array(
				"style" => "display: inline-block;",
				"title" => "This content is a good, entertaining and helpful.",
				"alt" => "Vote Up",
				"class" => "vote-link vote-up ui-icon ui-icon-plusthick rounded ".(($vote && $vote->vote == "up")?" ui-state-active":" ui-state-default"),
				"href" => ($profile)? $this->voteUrl($post, "up"): '#',
			), " ");
	}
	
	public function postStub($post, $options = array()) {
		$author = $post->tags->getTag("author")?:$post->tags->getTag("source");
		
		$parent = $post->_parent;
		switch($post->_type) {
			case 'answer':
			case 'question-comment':
				while($parent->_parent->_id) {
					$parent = $parent->_parent;
				}
				if($parent->export() == array()) return null;
				// $post = $parent;
				break;
		}

		if($parent->export() == array()) {
			$parent = $post;
		}
		
		return $this->htmlTag("div", array("class" => "post-stub rounded center-shadow ui-state-default", "id" => $post->_type."-".$post->id), 
			// $this->htmlTag("div", array("class" => "inline-flow"), ">")."". // Minimize / Maximize
			$this->htmlTag("div", array("class" => "stub-score rounded text-verylarge vote-count ".$this->color($this->scoring($post))), 
				$this->scoring($post)
			)."".
			$this->htmlTag("div", array("class" => "stub-title rounded text-large center-shadow"), 
				$this->htmlTag("div", array("class" => "stub-vote rounded inline-flow", "style" => "float: right"), $this->stubVote($post))."".
				$this->view->postLink($post, array("text" => $parent->title?:$post->title))
			)."".
			$this->htmlTag("div", array("class" => "stub-meta inline-flow font-sans"), 
				$this->htmlTag("span", array(), $this->view->profileLink($author))."".
				$this->htmlTag("span", array(), " ".$this->whatsThis($post))."".
				$this->htmlTag("span", array(), " ".$this->toWhat($post))."".
				$this->htmlTag("span", array(), " ○ ".$this->view->timeAgo($post->_created))
			)."".
			$this->htmlTag("div", array("class" => "stub-loadin"), ' ')	
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