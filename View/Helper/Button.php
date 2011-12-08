<?php
/**
 * EpicDb_View_Helper_FollowLink
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_Button extends MW_View_Helper_HtmlTag
{
	public function button($url, $route = null, $reset = true, $params = array()) {
		if(!empty($params['requirePriv']) && in_array($route, array('post', 'record', 'profile')) && !EpicDb_Auth::getInstance()->hasPrivilege($url[$route], $params['requirePriv'])) {
			return '';
		}
		$icon = 'gear';
		if (!empty($params['text'])) {
			$text = $params['text'];
		} else if (!empty($url['action'])) {
			$text = $url['action'];
		} else {
			$text = "Button";
		}

		$style = '';
		$external = null;
		$tooltip = null;
		$dataTooltip = null;
		if(isset($params['icon'])) $icon = $params['icon'];
		if(isset($params['style'])) $style = $params['style'];
		if(isset($params['class'])) $style .= " ".$params['class'];
		if(isset($params['url'])) $external = $params['url'];
		if(isset($params['data-tooltip'])) $dataTooltip = $params['data-tooltip'];
		$options = array(
			'class' => 'no-tooltip epicdb-button epicdb-button-icon-left ui-state-default ui-corner-all '.$style,
			'href' => $external?:$this->view->url($url, $route, $reset),
			'rel' => 'no-tooltip',
			'style' => $style,
		);
		if(isset($params['data-voteurl'])) $options['data-voteurl'] = $params['data-voteurl'];
		if($dataTooltip) $options['data-tooltip'] = $dataTooltip;
		if(isset($params['tooltip'])) $options['data-tooltip'] = $params['tooltip'];
		return $this->htmlTag("a", $options, $this->htmlTag("span", array(
					'class' => 'ui-icon ui-icon-'.$icon,
				), " ").
				"<span class='ui-button-label'>".$text."</span>"
		)."";
		// return "<a href='".$this->view->url(array(
		// 	'profile' => $profile,
		// 	'action' => 'follow',
		// ), "profile", true)."' class='no-tooltip r2-button ui-state-default r2-button-icon-left ui-corner-all'><span class='ui-icon ui-icon-gear'></span>".$text."</a>";
	}	
} // END class EpicDb_View_Helper_FollowLink