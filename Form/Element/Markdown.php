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
		// var_dump($this->getValue()); exit;
		$html = $this->getValue();
		$tempString = '][M]o[R]e[';
		$html = preg_replace('/<!--\s*more\s*-->/i', $tempString, $html);
		$this->_value = $html;
		$html = parent::getRenderedValue();
		$html = str_replace($tempString, "<!-- more -->", $html);
		$match = '/\{\s*(http:\/\/[\w.]*r2-db\.com\/skill-tree\/calculator\/\d+\/[^#}]*)(#build=\w{32})?\s*\}/i';
		$replace = '<iframe width="638" height="374" src="$1?format=iframe$2" frameborder="0" allowfullscreen scrolling="no"></iframe>';
		$html = preg_replace($match, $replace, $html);		
		$match = '/\{http:\/\/[\w.]*youtube\.com\/watch\?v=([^}]+)\}/';
		$replace = '<iframe width="620" height="348" src="http://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>';
		$html = preg_replace($match, $replace, $html);
		return $html;
	}
	
} // END class R2Db_Form_Element_Markdown