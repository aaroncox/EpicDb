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
	protected $_doc = false;
	protected $_target = false;
	protected static $_pageCache = array();

	public function wrap($content) {
		return $this->view->htmlTag("div", array(
			"class" => "r2-tooltip tooltip-rounded", 
			"id" => $this->_doc->_type."-".$this->_doc->id
			), 
			$this->view->htmlTag("div", array("class" => "tooltip-reset tooltip-inner ui-helper-clearfix"),
				$content
			)." "
		)." ";
	}
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
		$class = "";
		$type = $this->_doc->_type;
		switch($this->_doc->_type) {
			case "seed":
			case "item":
			case "skill":
			case "profession":
	 			$class = "tooltip-record-icon";
				break;
			default: 
				break;
		}
		return $this->view->htmlTag("div", array("class" => "tooltip-icon-box ".$class), 
			$this->view->htmlTag("div", array(
				"class" => "tooltip-icon tooltip-rounded",
				"style" => "background: url('".$this->_doc->getIcon()."') no-repeat top center",
			), " ")." ".
			(($helper = $this->_doc->_tooltipButtonHelper) ? $this->view->$helper($this->_doc) : '')
		)."";
	}
	public function name() {
		$color = array();
		if($this->_doc->quality) $color += array('class' => 'quality-'.$this->_doc->quality);
		return $this->_doc ? $this->view->htmlTag("h3", array()+$color, $this->view->recordLink($this->_doc, array("rel" => "no-tooltip"))).'' : '';
	}
	
	public function level() {
		return $this->view->htmlTag("p", array(), $this->_doc->reputation." Reputation");
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
								"text" => $this->view->htmlFragment($this->view->escape($parentDoc->getName()), 50)
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
								"text" => $this->view->htmlFragment($this->view->escape($parentDoc->getName()), 50),
								"rel" => "no-tooltip",
							)) : ''
							).'';
						;		
	}
	public function title() {
		if(!$this->_doc->getName()) return '';
		return $this->_doc ? $this->view->htmlTag("h3", array(), 
			($this->_doc->getName()) ? $this->view->postLink($this->_doc, array(
				"text" => $this->view->htmlFragment($this->view->escape($this->_doc->getName()), 50),
				"rel" => "no-tooltip",
			)) : ''
			).'' : '';
	}
	public function description() {
		$doc = $this->_doc;
		if (!$doc) return '';
		$desc = $doc->getDescription();
		if($desc instanceOf Shanty_Mongo_Document) $desc = $desc[0];
		if(is_array($desc)) return $this->rankedDescription();
		return $this->view->htmlTag("div", array("class" => "description"), $desc?:" ")." ";
	}
	public function rankedDescription() {
		$doc = $this->_doc;
		$descs = $doc->getDescription();
		$html = "";
		$spent = $this->_doc->getRank();
		$avail = $this->_doc->getMaxRank();
		$html .= $this->view->htmlTag("p", array('class' => 'points'), 
			$this->view->htmlTag("span", array('class' => 'rank-label'), "Rank: ")." ".
			$this->view->htmlTag("span", array('class' => 'spent'), $spent)." / ".
			$this->view->htmlTag("span", array('class' => 'avail'), $avail)
		);
		if(isset($descs['current'])) {
			$html .= $this->view->htmlTag("div", array('class' => 'currentEffect'), 
				$this->view->htmlTag("p", array('class' => 'rank-label'), 
					"Current Rank (".$spent."/".$avail."):".
					$descs['current']
				)
			);
		}
		if(isset($descs['next'])) {
			$html .= $this->view->htmlTag("div", array('class' => 'nextEffect'),
				$this->view->htmlTag("p", array('class' => 'rank-label'), 
					"Next Rank (".($spent+1)."/".$avail."):".
					$descs['next']
				)
			)."";
		}
		// var_dump($descs); exit;
		// echo htmlspecialchars($html); exit;
		return $html;
	}
	// 		<p class="points">
	// 			<span class="spent">0</span> / <span class="avail">3</span>
	// 		</p>
	// 		<p class="currentEffect"></p>
	// 		<p class="nextEffect">
	// 			Next Rank:</br>
	// 			<span class="next-text">Progressively slows the target from 90% to 10% movement speed over <span class="change">3 seconds</span> and deals <span class="change">43 kinetic damage</span> each second. At the end of the duration the target is crushed, and takes an additional <span class="change">250 kinetic damage</span>.</span>
	// 		</p>
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
	public function classRelation($class) {
		return $this->view->htmlTag("p", array("style" => "font-size: 10px"), $this->view->recordLink($class)." Adv. Class");
	}
	public function body() {
		$doc = $this->_doc;
		if (!$doc) return '';
		
		$description = $this->view->htmlFragment($doc->getDescription(), 250);
		if(!$description) return '';

		if($source = $doc->tags->getTag('source')) {
			$html = $this->view->htmlTag("p", array("style" => "font-style: italic"), "Posted on ".$this->view->profileLink($source));			
		} else {
			$profile = $doc->tags->getTag('author'); 
			$html = $this->view->htmlTag("p", array("style" => "font-style: italic"), $this->view->profileLink($profile)." writes...");
						
		}
		
		if($doc instanceOf EpicDb_Mongo_Post) {
			$html .= $description;
			$html .= $this->view->htmlTag("p", array(), $this->view->postLink($doc, array("text" => "Read More...")));			
		}

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
	public function mods($documentSet, $label = "") {
		$modsFound = false;
		$modsHtml = " ";
		foreach($documentSet as $mod) {
			if(!$mod->ref->id) continue; // We haven't resolved the mods yet...
			$modsFound = true;
			$color = array();
			$stats = $this->view->stats($this->_doc, $mod->ref->attribs, "mods");
			if($mod->ref->quality) $color += array('class' => 'quality-'.$mod->ref->quality);
			$newName = $mod->ref->name." (".$mod->ref->attribs->requireLevel.")";
			$modsHtml .= $this->view->htmlTag("span", array()+$color, $this->view->recordLink($mod->ref, array("text" => $newName)));
			$modsHtml .= $this->view->htmlTag("div", array("class" => "stats-group", "id" => "mod-".$mod->ref->id), $stats);
		}
		if(!$modsFound) return ''; // No mods found, don't render otherwise we break layout.
		return $this->view->htmlTag("div", array("class" => "mod-container"), 
			$this->view->htmlTag("h4", array("class" => "label header"), "Item Modifications:")."".
			$this->view->htmlTag("div", array("class" => "stats-group"), $modsHtml)
		);
	}
	public function statsgroup($attribs, $title) {
		if(empty($attribs)) return " ";
		return $this->view->htmlTag("h4", array("class" => "label header"), $title.":")."".
			$this->view->htmlTag("div", array("class" => "stats-group"), 
				$this->view->stats($this->_doc, $attribs, "totals")." "
			);
	}
	public function linkTo($document, $label = "") {
		return $this->view->htmlTag("h4", array("class" => "label"), $label.": ".$this->view->recordLink($document));
	}
	public function ulli($documentSet, $label) {
		$html = "";
		foreach($documentSet as $document) {
			$li = $this->view->recordLink($document->ref);
			if($document->qty) {
				$li = $document->qty."x ".$li;
			}
			$html .= $this->view->htmlTag("li", array(), $li)."";
		}
		$label = $this->view->htmlTag("h4", array("class" => "label"), $label)."";
		return $label.$this->view->htmlTag("ul", array(), $html);
	}
	public function cloud($documentSet, $label = "") {
		// return ''; // Disabled until we're sticky.
		return $this->view->htmlTag("div", array("class" => "tooltip-cloud"), 
			(empty($label)?"":$this->view->htmlTag("h4", array("class" => "label transparent-bg-blue inline-flow"), $label))."".
			$this->view->iconCloud($documentSet)
		);
	}
	public function appendQuestions($limit) {
		$doc = $this->_doc;
		$params = $this->_params;
		$query = array(
			'tags.ref' => $doc->createReference()
		);
		$sort = array(
			'votes.score' => -1
		);
		$questions = "";
		$results = EpicDb_Mongo::db('question')->fetchAll($query, $sort, $limit);
		if(count($results)) {
			$questions .= $this->view->htmlTag("div", array("class" => "question"), 
				$this->view->htmlTag("h3", array(), "Popular Questions").""
			)."";
			foreach($results as $question) {
				$questions .= $this->view->htmlTag("div", array("class" => "question"), 
					// $this->view->htmlTag("span", array("class" => "score"), $question->votes['score']?:0)."".			
					$this->view->htmlTag("span", array("class" => "name"), $this->view->postLink($question))
				)."";
			}			
		}
		return $questions;
	}
	public function render() {
		$doc = $this->_doc;
		$params = $this->_params;
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

		if(isset($params['appendQuestions'])) {
			$content .= $this->appendQuestions($params['appendQuestions']);
		}
		
		return $this->wrap($content)."";
	}
	public function __toString() {
		try {
			$content = $this->render();
		} catch( Exception $e ) {
			var_dump($this->_doc, $e); exit;
			$content = $e->getMessage();
		}
		return $content;
	}
	public function addToCache() {
		if($this->_doc == false) return $this;
		$doc = $this->_doc;
		$url = $this->view->url($doc->getRouteParams(), $doc->routeName, true);
		if(isset(self::$_pageCache[$url])) return $this;
		self::$_pageCache[$url] = true;
		self::$_pageCache[$url] = $this->render();
		return $this;
	}
	public function renderCache() {
		if(empty($_pageCache)) {
			return '';
		}
		return $this->view->htmlTag("script", array(), 
			'r2tip&&r2tip.addCache('.json_encode(self::$_pageCache).");"
		);
	}
	public function tooltip($document = false, $params = array()) {
		$this->_doc = false;
		$this->_params = $params;
		if($document instanceOf EpicDb_Interface_Tooltiped) {
			$this->_doc = $document;
		}
		if($document instanceOf EpicDb_Mongo_Seed && isset($params['target'])) {
			$this->_doc->setTarget($params['target']);
		}
		if(isset($params['rank'])) $this->_doc->setRank((int)$params['rank']);
		return clone $this;
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
