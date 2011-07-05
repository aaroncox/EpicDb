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
class EpicDb_View_Helper_RecordContext extends MW_View_Helper_HtmlTag
{
	public function recordContext(EpicDb_Mongo_Record $record) {
		$placeholder = $this->view->context();
		$placeholder->widget("[Suggestion Tool under Construction]");
	}
} // END class EpicDb_View_Helper_ProfileSummary