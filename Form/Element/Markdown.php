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
	protected $_purifyOptions = array(
		array("HTML.Nofollow", 1)
	);

	public function purifyConfig( $config, $options ) {
		$def = $config->getHTMLDefinition( true );
		$def->addElement( "spoiler", "Inline", "Inline", "Common" );
	}
	
	public function getRenderedValue()
	{
		$this->_purifyOptions[ "configCallback" ] = array( $this, "purifyConfig" );
		// var_dump($this->getValue()); exit;
		$html = $this->getValue();
		$tempString = '][M]o[R]e[';
		$html = preg_replace('/<!--\s*more\s*-->/i', $tempString, $html);
		$this->_value = $html;
		$html = parent::getRenderedValue();
		$html = str_replace($tempString, "<!-- more -->", $html);
		// Capture Links to Skill Trees and Convert to iframes
		$match = '/\{\s*(http:\/\/[\w.]*r2-db\.com\/skill-tree\/calculator\/\d+\/[^#}]*)(#build=\w{10,40})?\s*\}/i';
		$replace = '<iframe class="r2-embeded-skilltree" width="638" height="374" src="$1?format=iframe$2" frameborder="0" allowfullscreen scrolling="no"></iframe>';
		$html = preg_replace($match, $replace, $html);		
		// Capture YouTube links in {} and embed
		$match = '/\{http:\/\/[\w.]*youtube\.com\/watch\?v=([^}]+)\}/';
		$replace = '<iframe width="620" height="348" src="http://www.youtube.com/embed/$1?wmode=transparent" frameborder="0" allowfullscreen></iframe>';
		$html = preg_replace($match, $replace, $html);
		// Capture Record links in {} and embed icons w/ tooltip
		$match = '/\{\s*http:\/\/[\w.]*r2-db\.com\/(advanced-class|user|advanced-class|race|item|skill|npc|quest|mission|place|class|starship|companion|profession|crew-skill|achievement|tag|achievement|faction|website|user|guild|group)\/(\d+).*?\}/i';
		preg_match_all($match, $html, $results);
		// var_dump($match, $html, $results); exit;
		for($i = 0; $i < count($results[1]); $i++) {
			$record = EpicDb_Mongo::db($results[1][$i])->fetchOne(array("id" => (int) $results[2][$i]));
			if($record && $icon = $record->getIcon()) {
				$cleanUrl = str_replace(array("{","}"), "", $results[0][$i]);
				$replace = "<a href='".$cleanUrl."'><img src='".$icon."' class='record-icon inline-icon'/></a>";
				$html = str_replace($results[0][$i], $replace, $html);
			}
		}
		return $html;
	}
	
} // END class R2Db_Form_Element_Markdown