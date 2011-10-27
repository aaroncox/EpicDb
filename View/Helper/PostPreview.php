<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_View_Helper_PostPreview extends Zend_View_Helper_Abstract
{
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Aaron Cox
	 **/
	function postPreview($html, $preview = false)
	{
		if($preview) {
			$partial = preg_split("/<!--\s*more\s*-->/i", $html);
			$html = $partial[0];
		}
		return $html;
	}
} // END class EpicDb_View_Helper_PostPreview extends Zend_View_Helper_Abstract