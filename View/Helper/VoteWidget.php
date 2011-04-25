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
	public function voteWidget($post) {
		// Some default settings
		$score = 0;
		if(isset($post->votes['score'])) $score = $post->votes['score'];
		$vote = null;
		if($profile = EpicDb_Auth::getInstance()->getUserProfile()) {
			$vote = EpicDb_Mongo::db('vote')->getVoteByProfile($post, $profile);			
		}
		// Return the widget
		return $this->htmlTag("div", array("class" => "post-vote dark-bg rounded shadowy"), 
			$this->htmlTag("p", array("class" => "text-small", "style" => "margin: 5px 0; font-weight: bold;"), "VOTES")."".
			$this->htmlTag("a", array(
				"style" => "display: inline-block;",
				"title" => "This question/answer is a good question and is helpful",
				"alt" => "Vote Up",
				"class" => "vote-link vote-up ui-icon ui-icon-plus rounded ".(($vote && $vote->vote == "up")?" ui-state-active":" ui-state-default"),
				"href" => ($profile)? $this->voteUrl($post, "up"): '#',
			), " ")."".
			$this->htmlTag("p", array("class" => "vote-count".$this->color($score)), $score)."".
			$this->htmlTag("a", array(
				"style" => "display: inline-block;",
				"title" => "This question/answer is a unclear, not helpful or not answerable",
				"alt" => "Vote Down",
				"class" => "vote-link vote-down ui-icon ui-icon-minus rounded".(($vote && $vote->vote == "down")?" ui-state-active":" ui-state-default"),
				"href" => ($profile)? $this->voteUrl($post, "down"): '#',
			), " ")
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