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
class EpicDb_View_Helper_ProfileContext extends MW_View_Helper_HtmlTag
{
	public function profileContext(EpicDb_Mongo_Profile $profile) {
		$placeholder = $this->view->context();
		$placeholder->prepend($this->view->tooltip($profile));
	}
} // END class EpicDb_View_Helper_ProfileSummary