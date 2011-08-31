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
	public function recordTypeLink($record) {
		return $this->htmlTag("a", array("rel" => "nofollow", "href" => $record->_type."s"), ucfirst($record->_type));
	}
}