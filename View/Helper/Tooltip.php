<?php
/**
 * EpicDb_View_Helper_Tooltip
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_Tooltip extends Zend_View_Helper_Abstract 
{
	public function wrap($content) {
		return $this->view->htmlTag("div", array(
			"class" => "tooltip tooltip-rounded", 
			"id" => $this->_doc->_type."-".$this->_doc->id
			), 
			$this->view->htmlTag("div", array("class" => "tooltip-reset tooltip-inner ui-helper-clearfix"),
				$content
			).""
		)."";
	}
	protected $_doc = false;
	public function counter() {
		return '';
		// This is the code to add the counter to the page and make it look purdy.
		return $this->htmlTag("div", array("class" => "tooltip-icon-counter"), 
			$this->htmlTag("span", array("class" => "spent"), 0)."".
			$this->htmlTag("span", array("class" => "avail"), 0)
		);
	}
	public function link() {
		$doc = $this->_doc; 
		if(!$doc->url) return '';
		return $this->view->htmlTag("h4", array(), $this->view->externalTo($this->_doc->url));
	}
	public function icon() {
		if(!$icon = $this->_doc->getIcon()) return '';
		if(isset($this->_params['icon']) && $this->_params['icon'] == false) return '';
		return $this->view->htmlTag("div", array("class" => "tooltip-icon-box"), 
			$this->view->htmlTag("div", array(
				"class" => "tooltip-icon tooltip-rounded",
				"style" => "background: url('".$this->_doc->getIcon()."') no-repeat top center",
			), " ")."".
			$this->counter()
		)."";
	}
	public function name() {
		return $this->_doc ? $this->view->htmlTag("h3", array(), $this->view->recordLink($this->_doc, array("rel" => "no-tooltip"))).'' : '';
	}
	
	public function parentTitle() {
		$doc = $this->_doc;
		if(!$doc->getName()) return '';
		$profile = $doc->tags->getTag('author')?:$doc->tags->getTag('source');
		$parentDoc = $doc->tags->getTag('parent')?:$doc->_parent;
		if(!is_object($parentDoc)) return '';
		return $this->view->htmlTag("p", array("style" => "font-size: 10px"), "An ".$doc->_type." in response to...")."".
						$this->view->htmlTag("h3", array(), 
							($parentDoc->getName()) ? $this->view->postLink($parentDoc, array(
								"text" => $this->view->htmlFragment($parentDoc->getName(), 50)
							)) : ''
							).'';
						;		
	}
	public function subjectTitle() {
		$doc = $this->_doc;
		if(!$doc->getName()) return '';
		$profile = $doc->tags->getTag('author')?:$doc->tags->getTag('source');
		$parentDoc = $doc->tags->getTag('subject')?:$doc->_record;
		if(!is_object($parentDoc)) return '';
		return $this->view->htmlTag("p", array("style" => "font-size: 10px"), "A ".$doc->_type." on...")."".
						$this->view->htmlTag("h3", array(), 
							($parentDoc->getName()) ? $this->view->recordLink($parentDoc, array(
								"text" => $this->view->htmlFragment($parentDoc->getName(), 50),
								"rel" => "no-tooltip",
							)) : ''
							).'';
						;		
	}
	public function title() {
		if(!$this->_doc->getName()) return '';
		return $this->_doc ? $this->view->htmlTag("h3", array(), 
			($this->_doc->getName()) ? $this->view->postLink($this->_doc, array(
				"text" => $this->view->htmlFragment($this->_doc->getName(), 50),
				"rel" => "no-tooltip",
			)) : ''
			).'' : '';
	}
	public function description() {
		$doc = $this->_doc;
		if (!$doc) return '';
		return $this->view->htmlTag("div", array("class" => "description"), $doc->getDescription()?:" ");
	}
	public function limitDescription() {
		$doc = $this->_doc;
		if (!$doc) return '';
		return $this->view->htmlTag("div", array("class" => "description"), 
			$this->view->htmlFragment($doc->getDescription(), 250)?:" "
		);
		
	}
	public function follow() {
		return $this->view->followButton($this->_doc);
	}
	public function body() {
		$doc = $this->_doc;
		if (!$doc) return '';
		
		$description = $this->view->htmlFragment($doc->getDescription(), 250);
		if(!$description) return '';
		
		$profile = $doc->tags->getTag('author')?:$doc->tags->getTag('source');
		
		$html = $this->view->htmlTag("p", array("style" => "font-style: italic"), $this->view->profileLink($profile)." writes...");
		$html .= $description;
		$html .= $this->view->htmlTag("p", array(), $this->view->postLink($doc, array("text" => "Read More...")));

		return $this->view->htmlTag("div", array("class" => "description"), $html);
	}
	public function effect() {
		
	}
	public function interact() {
		
	}
	public function flavor() {
		
	}
	public function label($text, $escape = true) {
		return $this->view->htmlTag("h4", array("class" => "label"), $text, $escape);
	}
	public function cloud($documentSet, $label = "") {
		return $this->view->htmlTag("div", array("class" => "tooltip-cloud", "style" => "display: inline-block"), 
			$this->view->htmlTag("h4", array("class" => "label transparent-bg-blue inline-flow"), $label)."".
			$this->view->iconCloud($documentSet)
		);
	}
	public function render() {
		$doc = $this->_doc;
		if ($doc === false) {
			return '';
		}
		$helpers = $doc->getTooltipHelpers();
		$content = "";
		foreach ($helpers as $helper) {
			// check for local function called "helper" if not- assume another view helper that takes the document
			$args = array();
			if (is_array($helper)) {
				$args = $helper;
				$helper = array_shift($args);
			}
			if ($helper == 'tooltip') {
				return $this->tooltip(array_shift($args))."";
			}
			if (method_exists($this, $helper)) {
				$content .= call_user_func_array(array($this, $helper), $args);
			} else {
				array_unshift($args, $doc);
				$content .= call_user_func_array(array($this->view, $helper), $args);
			} 
		}
		return $this->wrap($content)."";
	}
	public function __toString() {
		try {
			$content = $this->render();
		} catch( Exception $e ) {
			$content = $e->getMessage();
		}
		return $content;
	}
	public function tooltip($document, $params = array()) {
		$this->_doc = false;
		$this->_params = $params;
		if($document instanceOf EpicDb_Interface_Tooltiped) $this->_doc = $document;
		return $this;
	}
} // END class R2Db_View_Helper_

// <div class="tooltip rounded" id="tooltip-example" style="margin-top: 17px;">
// 	<div class="tooltip-inner">
// 		<div class="icon-box">
// 			<div class="talent-icon rounded"></div>
// 			<div class="talent-counter">
// 				<span class="spent">0</span><span class="avail">3</span>
// 			</div>
// 		</div>
// 		<h3>Force Crush</h3>
// 		<div class="stats">
// 			<p class="cost">
// 				<span class="label">Force:</span> 4
// 			</p>
// 			<p class="cast">
// 				<span class="label">Instant</span>
// 			</p>
// 			<p class="cooldown">
// 				<span class="label">Cooldown:</span> 18 secs
// 			</p>
// 			<p class="range">
// 				<span class="label">Range:</span> 15m
// 			</p>
// 		</div>
// 		<p class="points">
// 			<span class="spent">0</span> / <span class="avail">3</span>
// 		</p>
// 		<p class="currentEffect"></p>
// 		<p class="nextEffect">
// 			Next Rank:</br>
// 			<span class="next-text">Progressively slows the target from 90% to 10% movement speed over <span class="change">3 seconds</span> and deals <span class="change">43 kinetic damage</span> each second. At the end of the duration the target is crushed, and takes an additional <span class="change">250 kinetic damage</span>.</span>
// 		</p>
// 		<p class="addPoint">Left click to add point</p>
// 	</div>
// </div>
