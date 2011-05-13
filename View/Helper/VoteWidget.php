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
	);

	public function makeVoteButton($vote)
	{
		$post = $this->_post;
		$dbVote = null;
		$tagOpts = array(
			"style" => "display: inline-block;",
			"alt" => "Vote Up",
			"class" => "vote-link vote-".$vote." ui-icon ".$this->_iconClass[$vote]." rounded ",
		);
		$tag = "span";
		if ($dbVote = EpicDb_Vote::factory( $post, $vote )) {
			if ($message = $dbVote->isDisabled()) {
				if ($vote == "accept") return "";
				$tagOpts["class"] .= " ui-state-disabled";
				$tagOpts["title"] = $message;
			} else {
				$tagOpts["data-voteurl"] = $this->voteUrl($post, $vote);
				$tagOpts["class"] .= (( $dbVote && $dbVote->hasCast() ) ? " ui-state-active" : " ");
				$tagOpts["href"] = "#";
				$tagOpts["title"] = $dbVote->linkTitle;
				$tag = "a";
			}
			return $this->view->htmlTag($tag, $tagOpts, " ");
		} else {
			return "";
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
			if (!empty($this->_opts['title'])) {
				$content .= $this->view->htmlTag("p", array(
						"class" => "text-verysmall font-sans", 
						"style" => "margin: 5px 0; font-weight: bold;"
					), $this->_opts['title']);
			}
			$content .= $this->makeVoteButton("up");
			// using another htmlTag so we don't render using our render ours up...
			$content .= $this->view->htmlTag("p", array("class" => "rounded vote-count".$this->color($score)), $score);
			if (!$post instanceOf EpicDb_Vote_Interface_UpOnly) {
				$content .= $this->makeVoteButton("down");
			}
			if ($post instanceOf EpicDb_Vote_Interface_Acceptable) {
				if ($post->votes['accept']) {
					$content .= "<div class='is-accepted tc-shadow tc-epic'> ✓ </div>";
				}
				$content .= $this->view->htmlTag("p", array(), $this->makeVoteButton("accept")."");
			}
		}
		$this->htmlTag("div", array("class" => 'vote-widget ' . @$this->_opts['class'] ?: ''), $content);
		return parent::render();
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