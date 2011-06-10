<?php
/**
 * EpicDb_View_Helper_QuestionLink
 *
 * Builds the link to a profile, using the profile route.
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_QuestionLink extends MW_View_Helper_HtmlTag
{
	public function questionLink($question, $params = array()) {
		if (!is_object($question)) {
			return '';
		}
		$text = $question->title;
		if(isset($params['text'])) {
			$text = $params['text'];
		}
		$slug = new MW_Filter_Slug();
		return $this->htmlTag("a", array(
			"rel" => 'no-tooltip nofollow',
			"href" => $this->view->url(array(
				'post' => $question,
				'slug' => $slug->filter($question->title),
			), 'questions', true),
		), (string) $text);
	}
}