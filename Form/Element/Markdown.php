<?php
/**
 * R2Db_Form_Element_Markdown
 *
 * undocumented class
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Form_Element_Markdown extends MW_Form_Element_Markdown {
	protected $_purifyOptions = array(array("HTML.Nofollow", 1));
	
	public function getRenderedValue()
	{
		$html = parent::getRenderedValue();
		$match = '/\{http:\/\/[\w.]*youtube.com\/watch\?v=([^}]+)\}/';
		$replace = '<iframe width="620" height="348" src="http://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>';
		$html = preg_replace($match, $replace, $html);
		return $html;
	}
	
} // END class R2Db_Form_Element_Markdown