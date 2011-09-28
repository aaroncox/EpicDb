<?php
/**
 * EpicDb_View_Helper_VoteWidget
 *
 * undocumented class
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_VoteWidget extends MW_View_Helper_HtmlTag
{
	public function voteUrl($post, $vote) {
		return $this->view->url(array(
			'type' => $post->_type,
			'id' => $post->id,
			'vote' => $vote,
		), 'vote-cast', true);
	}
	
	protected $_iconClass = array(
		'up' => 'ui-icon-plus',
		'down' => 'ui-icon-minus',
		'accept' => 'ui-icon-check',
		'flag' => 'ui-icon-alert',
		'spam' => 'ui-icon-alert',
		'offensive' => 'ui-icon-alert',
		'moderator' => 'ui-icon-alert',
	);

	protected $_flagTypes = array(
		"spam" => "Spam",
		"offensive" => "Offensive",
		"moderator" => "Needs Moderator Attention",
	);

	public function makeVoteButton($vote)
	{
		$post = $this->_post;
		$dbVote = null;
		$tagOpts = array(
			"style" => "display: inline-block;",
			"alt" => "Vote",
			"class" => "vote-link vote-".$vote." rounded",
		);
		$iconClass = " ui-icon ".$this->_iconClass[$vote];
		$tag = "span";
		if ($vote == 'flag') {
			if ($post instanceOf EpicDb_Vote_Interface_Flaggable) {
				$tagOpts['class'] .= $iconClass;
				$content = $this->view->htmlTag( "a", $tagOpts, " " )."<ul class='vote-flag-popout ui-widget ui-state-default' style='display:none'>";
				foreach(array_keys($this->_flagTypes) as $type) $content .= "<li>".$this->makeVoteButton( $type )."</li>";
				$content .= "</ul>";
				return $content;
			}
			return "";
		} else {
			$dbVote = EpicDb_Vote::factory( $post, $vote );
			$content = " ";
			if (isset($this->_flagTypes[$vote])) {
				$content = "<span class='$iconClass' style='display: inline-block'> </span>";
				$content .= $this->_flagTypes[$vote];
				if($dbVote) {
					if ($vote == "moderator") $content .= "<br><input type='text' name='reason' placeholder='Reason for flagging' value='".$this->view->escape($dbVote->reason)."'>";					
				}
			} else {
				$tagOpts['class'] .= $iconClass;
			}

			if (!$dbVote || $message = $dbVote->isDisabled()) {
				if(!isset($message)) $message = "You must be logged in to perform this action.";
				$tagOpts["class"] .= " ui-state-disabled has-tooltip";
				$tagOpts["data-tooltip"] = $message;
			} else {
				$tagOpts["data-voteurl"] = $this->voteUrl($post, $vote);
				$tagOpts["class"] .= (( $dbVote && $dbVote->hasCast() ) ? " ui-state-active" : " ");
				$tagOpts["href"] = "#";
				$tagOpts["title"] = $dbVote->linkTitle;
				switch($vote) {
					case "up":
						$tagOpts["class"] .= " has-tooltip";
						$tagOpts["data-tooltip"] = "Up Vote - This post was useful, informative and helpful.";
						break;
					case "down":
						$tagOpts["class"] .= " has-tooltip";
						$tagOpts["data-tooltip"] = "Down Vote - This post was irrelevant, inflammatory, inaccurate or off-topic.";
						break;
					default: 
						break;
				}
				$tag = "a";
			}
			return $this->view->htmlTag($tag, $tagOpts, $content);
		}
	}
	protected $_post = null;
	protected $_opts = array();

	public function render()
	{
		$post = $this->_post;
		if (!$post) {
			return " ";
		}
		$score = 0;
		if(isset($post->votes['score'])) $score = $post->votes['score'];
		// Return the widget
		$content = " ";
		// Move this somewhere, I haven't found it yet
		if ($post instanceOf EpicDb_Vote_Interface_Votable) {
			// if (!empty($this->_opts['title'])) {
			// 	$content .= $this->view->htmlTag("p", array(
			// 			"class" => "text-verysmall font-sans", 
			// 			"style" => "margin: 5px 0; font-weight: bold;"
			// 		), $this->_opts['title']);
			// }
			$content .= $this->view->htmlTag("div", array("class" => "transparent-bg-blue rounded padded"),
				$this->view->htmlTag("div", array("class" => "vote-count".$this->color($score)), $score)." ".
				$this->view->htmlTag("div", array(), 
					$this->makeVoteButton("up")." ".
					((!$post instanceOf EpicDb_Vote_Interface_UpOnly) ? $this->makeVoteButton("down") : " ")
				)."".
				(($post instanceOf EpicDb_Vote_Interface_Acceptable) ? $this->view->htmlTag("p", array(), $this->makeVoteButton("accept")."") : "")
			);
			
		}
		if ($post instanceOf EpicDb_Vote_Interface_Flaggable) {
			$content .= $this->view->htmlTag("p", array(), $this->makeVoteButton("flag")." ");
		}
		return $this->view->htmlTag("div", array("class" => 'vote-widget ' . @$this->_opts['class'] ?: ''), $content)." ";
	}
	
	public function voteWidget($post, array $opts = array()) {
		if ($post) $this->_post = $post;
		$this->_opts = $opts;
		// Some default settings
		
		return $this;
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
} // END class EpicDb_View_Helper_VoteWidget

/*
<div class="vote">
	<p class="text-small" style="margin: 5px 0; font-weight: bold">VOTE</p>
	<div style="margin-left: auto; margin-right: auto;" title="This question is useful and clear (click again to undo)" alt="vote up" class="vote-up sprite sprite-vote-arrow-up"></div>
	<span class="vote-count-post">
		<?= $this->question->votes['score'] ?>
	</span>
	<div style="margin-left: auto; margin-right: auto;" title="This question is unclear or not useful (click again to undo)" alt="vote down" class="vote-down sprite sprite-vote-arrow-down"></div>
</div>
*/