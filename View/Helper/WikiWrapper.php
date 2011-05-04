<?php
/**
 * EpicDb_View_Helper_InlineWrapper
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_WikiWrapper extends Zend_View_Helper_Abstract
{
	public function wrap($header, $content, $params = array()) {
		$classes = "rounded";
		if(isset($params['class'])) {
			$classes .= " ".$params['class'];
		}
		if(isset($params['wiki']) && $params['wiki'] instanceOf EpicDb_Mongo_Wiki) {
			// FIX ME!!!!
			if(MW_Auth::getInstance()->getUser()) {
				if(MW_Auth::getInstance()->getUser()->isMember(MW_Auth_Group_Super::getInstance())) {
					$header .= " (<a href='/wiki/".$params['wiki']->record->_id."/".$params['wiki']->type."'>Edit</a>)";				
				}
			}
		}
		return "<div class='wiki-block rounded center-shadow padded-10'>
			<h2 class='section-header'>
				".$header." 
			</h2>
			<div class='wiki-content ".$classes."'>
			".$content."
			</div>
		</div>";
	}
	
	public function inlineWrapper() {
		throw new Exception("This view helper hasn't been modified for direct usage (yet!)");
	}
} // END class EpicDb_View_Helper_InlineWrapper