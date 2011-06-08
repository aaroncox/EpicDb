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
			"class" => "tooltip rounded", 
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
	public function icon() {
		return $this->view->htmlTag("div", array("class" => "tooltip-icon-box"), 
			$this->view->htmlTag("div", array(
				"class" => "tooltip-icon rounded",
				"style" => "background: url('".$this->_doc->getIcon()."') no-repeat top center",
			), " ")."".
			$this->counter()
		)."";
	}
	public function name() {
		return $this->_doc ? $this->view->htmlTag("h3", array(), $this->_doc->getName()).'' : '';
	}
	public function description() {
		$doc = $this->_doc;
		if (!$doc) return '';
		return $this->view->htmlTag("div", array("class" => "description"), $doc->getDescription()?:" ");
	}
	public function effect() {
		
	}
	public function interact() {
		
	}
	public function flavor() {
		
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
	public function tooltip($document) {
		$this->_doc = false;
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
