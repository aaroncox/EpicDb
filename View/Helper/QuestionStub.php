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
		if($question->findAnswers(false, array('score.accepted' => array('$gt' => 0)))->count() > 0) {
			$answerStatus = ' answered-accepted'; 
		} elseif($answerCount > 0) {
			$answerStatus = ' answered';
		}

		$author = $question->tags->getTag('author');
		$cardDetails = array(
			"posted this" => $this->view->timeAgo($question->_created)
		);
		
		// var_dump($question->export()); exit;
		
		if($question->touched && $question->touchedBy instanceOf EpicDb_Mongo_Profile) {
			$author = $question->touchedBy;
			$cardDetails = array(
				"updated this" => $this->view->timeAgo($question->touched)
			);
			
		}

		$link = $this->view->questionLink($question);
		// Shrinking Number Logic (Should prob be a view helper?)
		$views = $question->viewCount;
		if($question->viewCount >= 1000) {
			$views = round($views / 1000,0)."k";
		}
		
		// Loop through and generate tag block
		foreach($question->tags as $tag) {
			if($tag->reason == 'tag') {
				$tags .= $this->view->recordLink($tag->ref, array("class" => "post-tag"));
			}
		}
		
		return $this->htmlTag('div', array('class' => 'question-summary'), 
			$this->htmlTag('div', array('style' => 'cursor:pointer'), 
				$this->htmlTag('div', array('style' => 'float: right'), 
					$this->view->card($author, array(
						"class" => "medium-icon", 
						"content" => $cardDetails,
					)
				))."".
				$this->htmlTag('div', array('class' => 'votes'), 
					$this->htmlTag('div', array('class' => 'mini-counts'), isset($question->votes['score'])? $question->votes['score'] : 0)."".
					$this->htmlTag('div', array(), 'votes')
				)."".
				$this->htmlTag('div', array('class' => 'status'.$answerStatus), 
					$this->htmlTag('div', array('class' => 'mini-counts'), $answerCount)."".
					$this->htmlTag('div', array(), 'answers')
				)
			)."".
			$this->htmlTag('div', array('class' => 'summary'),
				$this->htmlTag("h3", array(), 
					$this->view->seQuestionLink($question)
				)."".
				$this->htmlTag('div', array('class' => 'tags'), $tags)
			)
		);

	}
} // END class EpicDb_View_Helper_QuestionStub