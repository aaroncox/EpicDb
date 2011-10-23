<?php
/**
 * EpicDb_View_Helper_WikiInline
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_WikiInline extends EpicDb_View_Helper_WikiWrapper
{
	public function wikiInline($record, $section, $params = array()) {
		$wiki = EpicDb_Mongo::db('wiki')->get($record, $section, false);
		if($wiki) {
			$header = $record->name." - ".$wiki->header;
			return $this->wrap($header, $wiki->html, array('wiki' => $wiki)+$params);			
		}
		if(isset($params['returnEmpty']) && $params['returnEmpty'] == true) {
			return '';
		}
		return $this->wrap("Incomplete Wiki called '".ucfirst($section)."'", "This wiki entry has not yet been created. If you are the owner of this page, or have been given permissions, you should see an edit button that will allow you to fill this section of the site out.", array('wiki' => new EpicDb_Mongo_Wiki(array('record' => $record, 'type' => $section)))+$params);
	}
} // END class EpicDb_View_Helper_WikiInline