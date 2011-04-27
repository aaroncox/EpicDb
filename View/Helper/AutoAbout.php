<?php
/**
 * EpicDb_View_Helper_AutoAbout
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_AutoAbout extends MW_View_Helper_HtmlTag
{	
	public function autoAbout($record) {
		$about = "";
		
		// This function is going to be a huge, ugly if statement, until a better solution is found...
		if($record->characters && $char = $record->characters[0]) {
			// Will be improved as characters are fleshed out.
			$about .= ' Primarily plays as '.$this->view->armoryLink($char);
		}
		
		return $about;
	}
} // END class EpicDb_View_Helper_AutoAbout