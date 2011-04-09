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
	public function voteWidget($post) {
		// Some default settings
		$score = 0;
		if(isset($post->votes['score'])) $score = $post->votes['score'];
		$vote = null;
		if($profile = EpicDb_Auth::getInstance()->getUserProfile()) {
			$vote = EpicDb_Mongo::db('vote')->getVoteByProfile($post, $profile);			
		}
		// Return the widget
		return $this->htmlTag("div", array("class" => "vote"), 
			$this->htmlTag("p", array("class" => "text-small", "style" => "margin: 5px 0; font-weight: bold;"), "VOTE")."".
			$this->htmlTag("a", array(
				"style" => "display: block;",
				"title" => "This question is a good question and is helpful",
				"alt" => "Vote Up",
				"class" => "vote-up sprite sprite-vote-arrow-up".(($vote && $vote->vote == "up")?"-on":""),
				"href" => $this->view->url(array(
					'type' => $post->_type,
					'id' => $post->id,
					'vote' => 'up',
				), 'vote-cast', true),
			), " ")."".
			$this->htmlTag("span", array("class" => "vote-count-post"), $score)."".
			$this->htmlTag("a", array(
				"style" => "display: block;",
				"title" => "This question is a good question and is helpful",
				"alt" => "Vote Down",
				"class" => "vote-down sprite sprite-vote-arrow-down".(($vote && $vote->vote == "down")?"-on":""),
				"href" => $this->view->url(array(
					'type' => $post->_type,
					'id' => $post->id,
					'vote' => 'down',
				), 'vote-cast', true),
			), " ")
		);
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