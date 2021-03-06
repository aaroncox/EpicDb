<?php
/**
 * EpicDb_View_Helper_RecordLink
 *
 * Builds the link to a record, using the record route.
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_RecordTypeLink extends MW_View_Helper_HtmlTag
{
	public function recordTypeLink($record, $params = array()) {
		$cleaned = ucwords(str_replace("-", " ", $record->_type));
		if($record->listName) $cleaned = $record->listName;
		if(!$record->_type || $record->noTypeList) {
			return $cleaned;
		}
		$itemprop = null;
		if(isset($params['itemprop'])) {
			$itemprop = $params['itemprop'];
			$cleaned = $this->view->htmlTag("span", array("itemprop" => "title"), $cleaned);
		}
		return $this->htmlTag("a", array(
			"rel" => "nofollow", 
			"itemprop" => $itemprop,
			"href" => $this->view->url(
				array(
				), $record->_type."_list", true
			)
		), $cleaned);
	}
}