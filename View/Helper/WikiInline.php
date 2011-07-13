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
	public function wikiInline($record, $section) {
		$wiki = EpicDb_Mongo::db('wiki')->get($record, $section, false);
		if($wiki) {
			$header = $record->name." - ".$wiki->header;
			return $this->wrap($header, $wiki->html, array('wiki' => $wiki));			
		}
		return $this->wrap("Incomplete Entry @ ".$section, "This section has yet to be created, please click edit and create this section.", array('wiki' => new EpicDb_Mongo_Wiki(array('record' => $record, 'type' => $section))));
	}
} // END class EpicDb_View_Helper_WikiInline