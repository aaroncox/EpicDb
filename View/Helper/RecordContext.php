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
		// We will have to move this into an ad management of some sort, but I just wanted to see how it looks / play with layout
		$placeholder->insertAd((string)$this->htmlTag("a", array("href" => "http://www.enjin.com/?ref=485091", "rel" => "no-follow"), 
			$this->htmlTag("img", array("src" => "http://www.enjin.com/images/affiliate/ads/enjin_vertical_1.jpg"), "")
		));

		$placeholder->widget($this->view->render("./_context/3c-friends.phtml"));		
	}
} // END class EpicDb_View_Helper_ProfileSummary