<?php
/**
 * EpicDb_View_Helper_QuestionStub
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_QuestionStub extends MW_View_Helper_HtmlTag
{
	public function questionStub($question) {
		$tags = ' '; // Need to render tags into this
		$tagsNames = ''; // This is a merged array of the tags, all with 't-' in front of the tag, for CSS purposes.
		$viewStatus = ''; //' warm'; // This is how "hot" the question is, colors the text.
		
		$answerCount = $question->findAnswers()->count();

		$answerStatus = ' unanswered';
		if($question->findAnswers(false, array('votes.accept' => array('$gt' => 0)))->count() > 0) {
			$answerStatus = ' answered-accepted'; 
		} elseif($answerCount > 0) {
			$answerStatus = ' answered';
		}

		$author = $question->tags->getTag('author');
		
		$cardDetails = array(
			"posted this" => $this->view->timeAgo($question->_created)
		);
		
		// var_dump($question->export()); exit;
		
		if($question->touched && $question->touchedBy instanceOf EpicDb_Mongo_Profile_User) {
			$author = $question->touchedBy;
			$cardDetails = array(
				"updated this" => $this->view->timeAgo($question->touched)
			);
			
		}

		$link = $this->view->postLink($question);
		// Shrinking Number Logic (Should prob be a view helper?)
		$views = $question->viewCount;
		if($question->viewCount >= 1000) {
			$views = round($views / 1000,0)."k";
		}
		
		// Loop through and generate tag block
		foreach($question->tags as $tag) {
			if($tag->reason == 'tag') {
				$state = 'ui-state-default';
				$profile = EpicDb_Auth::getInstance()->getUserProfile();
				if($profile && $profile->isFollowing($tag->ref)) {
					$state = 'ui-state-active';
				}
				$tags .= $this->view->card($tag->ref, array(
					"class" => "post-tag ".$state,
					"iconClass" => "tag-icon", 
					"content" => false,
				));
			}
		}
		// var_dump($question->tags->getTag('author')); 
		$voteClass = "votes font-sans inline-flow";
		$voteParams = array();
		if($question->isReputationDisabled()) {
			$voteClass .= " has-tooltip ui-state-disabled";
			$voteParams['data-tooltip'] = "This question has been marked as a 'Community Post' disabling all reputation gains/losses. Please read the Q&A FAQ for more information!";
		}
		return $this->htmlTag('div', array('class' => 'question-summary ui-helper-clearfix rounded shadowy transparent-bg-blue'), 
			$this->htmlTag('div', array('style' => 'float: right'), 
				$this->view->card($author, array(
					"class" => "medium-icon hide-info", 
					"content" => $cardDetails,
				)
			))."".
			$this->htmlTag('div', array('class' => $voteClass)+$voteParams, 
				$this->htmlTag('div', array('class' => 'mini-counts'), isset($question->votes['score'])? $question->votes['score'] : 0)."".
				$this->htmlTag('div', array('class' => 'mini-label'), 'votes')
			)."".
			$this->htmlTag('div', array('class' => 'status font-sans inline-flow'.$answerStatus), 
				$this->htmlTag('div', array('class' => 'mini-counts'), $answerCount)."".
				$this->htmlTag('div', array('class' => 'mini-label'), 'answers')
			)."".
			$this->htmlTag('div', array('class' => 'summary inline-flow'),
				$this->htmlTag("h3", array('class' => 'text-verylarge'), 
					$this->view->postLink($question, array("rel" => "no-tooltip"))
				)."".
				$this->htmlTag("p", array('class' => 'text-medium'), 
					$this->view->htmlFragment(strip_tags($question->body), 150)
				)."".
				$this->htmlTag('div', array('class' => 'tags'), $tags)
			)
		);

	}
} // END class EpicDb_View_Helper_QuestionStub