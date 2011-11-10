<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Badge_Helper_VoteScore extends EpicDb_Badge_Helper_Vote
{
	public function event(EpicDb_Event_Abstract $event) {
		if($event->getType() == 'vote') {
			$vote = $event->data['vote'];
			if(!in_array($vote->vote, array("up", "down"))) {
				return;
			}
			$profile = $vote->target;
			$post = $vote->post;
			if($this->hasBadge($profile, array("post" => $post->createReference()))) {
				return;
			}
			$score = $this->getOption('score');
			if($post->votes['score'] >= $score) {
				$this->awardTo($profile, array("post" => $post->createReference()));
			}
		}
	}
} // END class EpicDb_Badge_Helper_Vote_Score extends EpicDb_Badge_Helper_Vote