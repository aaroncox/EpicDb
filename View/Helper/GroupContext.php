<?php
/**
 * EpicDb_View_Helper_ProfileSummary
 *
 * undocumented class
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_GroupContext extends MW_View_Helper_HtmlTag
{
	public function groupContext(EpicDb_Mongo_Profile $profile) {
		$placeholder = $this->view->context();
		$placeholder->prepend($this->view->tooltip($profile));
		if($profile->description) $placeholder->widget($this->htmlTag("h3", array(), "Description")."".$this->htmlTag("p", array(), $profile->description));
		$placeholder->widget($this->view->render('./_context/recent-news-group.phtml'));
	}
} // END class EpicDb_View_Helper_ProfileSummary