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
	static public $score = 'score'; // Changes what vote type is displayed on the score. 
	public function toWhat($post) {
		$tags = array();
		foreach($post->tags->getTags('tag') as $tag) {
			if($tag['reason'] != 'tag' && $tag['reason'] != 'subject') continue;	
			$tags[] = (string) $this->view->recordLink($tag);
		}
		// var_dump($tags); exit;
		if(empty($tags)) return '';
		return " about ".implode(', ', $tags);
	}
	public function whatsThis($post) {
		switch($post->_type) {
			case 'question':
				$subject = $post->tags->getTag('subject');
				if($subject) {
					$type = ' question about '.$this->view->recordLink($subject);
				} else {
					$type = ' question';
				}
				break;
			case 'question-comment':
				$type = ' comment on '.$this->view->postLink($post->_parent, array("text" => "this ".$post->_parent->_type));
				break;
			case 'comment':
				$subject = $post->tags->getTag('subject');
				$parent = $post->_parent;
				if($subject || $parent) {
					if($parent instanceOf EpicDb_Mongo_Comment || $subject) {
						$type = ' comment on '.$this->view->recordLink($subject?:$parent);						
					} elseif($parent instanceOf EpicDb_Mongo_Post && $parent->_id) {
						$type = ' comment on '.$this->view->postLink($parent);
					} else {
						$type = ' comment';
					}
				} else {
					$type = ' comment';
				}
				break;
			case 'article':
			case 'article-rss':
				$type = 'n article';
				$author = $post->tags->getTag('author');
				$source = $post->tags->getTag('source');
				if($author && $source && $author != $source) {
					$type .= " on ".$this->view->profileLink($source);
				}
				break;
			case "message":
				$subject = $post->tags->getTag('subject');
				if($subject) {
					$modifier = "about";
					if($subject instanceOf EpicDb_Mongo_Profile) {
						$modifier = "to";
					}
					$type = ' message '.$modifier.' '.$this->view->profileLink($subject);
				} elseif($post->_parent->id) {
					$type = ' response to '.$this->view->postLink($post->_parent);
				} else {
					$type = 'n announcement.';
				}
				break;
			case "answer":
				$type = 'n answer to '.$this->view->postLink($post->_parent, array("text" => "this question"));
				break;
			default:
				$type = ' '.$post->_type;
				break;
		}
		return " posted a".$type;
	}
	
	public function scoring($post) {
		if(isset($post->votes[static::$score])) {
			return $post->votes[static::$score];
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
		$buttons = " ";
		if ($post instanceOf EpicDb_Vote_Interface_Votable) {
			$buttons .= $this->htmlTag("span", array("class" => "vote-label"), "YOUR VOTE!");
			if (!$post instanceOf EpicDb_Vote_Interface_UpOnly) {
				$buttons .= $this->view->voteWidget($post)->makeVoteButton('down');
			}
			$buttons .= $this->view->voteWidget($post)->makeVoteButton('up');
			if ($post instanceOf EpicDb_Vote_Interface_Acceptable) {
				$buttons .= $this->htmlTag("span", array("class" => "vote-label"), "ACCEPT ANSWER");
				$buttons .= $this->view->voteWidget($post)->makeVoteButton('accept');
			}
			if ($post instanceOf EpicDb_Vote_Interface_Flaggable) {
				$buttons .= "<br/>".$this->view->voteWidget($post)->makeVoteButton('flag');				
			}
		}
		return $buttons;
	}
	
	public function postHeader($post, $options) {
		if($post->title) return $post->title;
		if($post->body) return $this->view->htmlFragment(strip_tags($post->body), 60);
		return "no title";
	}
	
	public function showIcon($post, $options) {
		if(isset($options['headerIcon']) && $options['headerIcon'] == true) {
			$author = $post->tags->getTag('author')?:$post->tags->getTag('source');
			if($author instanceOf EpicDb_Mongo_Profile) {
				return $this->htmlTag('img', array('src' => $author->getIcon(), 'class' => 'feed-icon'));
			}
		} 
		return " ";
	}
	
	public function postStub($post, $options = array()) {

		// If we find a cache for this postStub, just return it.
		// TODO - The votewidget was being cached inside the cache, which caused it not to load if the first person looking at the cache was 
		// 				an anonymous user. So, I'm not sure how to tackel this...
		// if($postStub = EpicDb_Cache::load($post, 'postStub')) {
		// 	return $postStub;
		// }
		
		$author = $post->tags->getTag("author")?:$post->tags->getTag("source");
		
		$parent = $post->_parent;
		switch($post->_type) {
			case 'answer':
			case 'question-comment':
				while($parent->_parent->_id) {
					$parent = $parent->_parent;
				}
				if($parent instanceOf EpicDb_Mongo_Post && $parent->export() == array()) return null;
				// $post = $parent;
				break;
		}
		
		$wrapClass = '';
		if(isset($options['wrapClass'])) {
			$wrapClass = $options['wrapClass'];
		}

		if($post->_deleted) {
			$headerClass = 'transparent-bg-red ';
		} else {
			$headerClass = '';			
		}
		if(isset($options['headerClass'])) {
			$headerClass .= $options['headerClass'];
		}
		
		$voteClass = '';
		if(isset($options['voteClass'])) {
			$voteClass = $options['voteClass'];
		}
 
		if(!is_object($parent)) {
			$parent = $post;
		}
		
		$html = $this->htmlTag("div", array("class" => "post-stub rounded center-shadow ui-helper-clearfix ".$wrapClass, "id" => $post->_type."-".$post->id), 
			// $this->htmlTag("div", array("class" => "inline-flow"), ">")."". // Minimize / Maximize
			$this->htmlTag("div", array("class" => "stub-score rounded ".$voteClass.$this->color($this->scoring($post))), 
				$this->htmlTag("span", array("class" => "vote-label"), ucfirst(static::$score))."".
				$this->htmlTag("p", array("class"=>"vote-count"), $this->scoring($post))."".
				$this->htmlTag("span", array("class" => "vote-controls vote-widget"),
					$this->stubVote($post)
				)
			)."".
			$this->htmlTag("div", array("class" => "stub-title rounded text-large center-shadow ".$headerClass), 
				// $this->htmlTag("div", array("class" => "stub-vote rounded inline-flow", "style" => "float: right"), $this->stubVote($post))."".
				// $this->view->profileLink($post->tags->getTag('author')?:$post->tags->getTag('source'))." POSTS ".
				$this->showIcon($post, $options)."".
				$this->view->postLink($post, array("text" => $this->postHeader($post, $options)))
			)."".
			$this->htmlTag("div", array("class" => "stub-meta font-sans"), 
				$this->htmlTag("span", array(), $this->view->timeAgo($post->_created)." ○ ")."".
				$this->htmlTag("span", array(), ($author) ? $this->view->profileLink($author) : 'an anonymous user')."".
				$this->htmlTag("span", array(), " ".$this->whatsThis($post))."".
				$this->htmlTag("span", array(), " ".$this->toWhat($post))
			)."".
			$this->htmlTag("div", array("class" => "stub-loadin"), ' ')	
		);
		// EpicDb_Cache::save($post, 'postStub', $html);
		return $html;
		
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