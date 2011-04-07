<?php
/**
 * EpicDb_View_Helper_Card
 *
 * Builds the "card" version of a record
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_Card extends MW_View_Helper_HtmlTag
{
	public function link($record, $params = array()) {
		if($record instanceOf EpicDb_Mongo_Profile) {
			return $this->view->profileLink($record, $params);
		}
		return $this->view->recordLink($record, $params);
	}
	public function addExtra($extra) {
		return $this->htmlTag("p", array("class" => 'text-small'), $extra);
	}
	public function card($record, $params = array('extra' => null)) {
		return 
			$this->htmlTag("div", array('class' => 'record-icon inline-flow rounded'), 
				$this->link($record, array("text" => $this->htmlTag("img", array('src' => $record->getIcon()))))				
			)."".
			$this->htmlTag("div", array('class' => 'record-info inline-flow'), 
				$this->htmlTag("h2", array('class' => 'text-large'), $this->link($record))."".
				$this->htmlTag("p", array('class' => 'text-verysmall'), "is a")."".
				$this->htmlTag("p", array('class' => 'text-small'), $this->view->recordTypeLink($record))."".
				$this->addExtra($params['extra'])
			);
	}
}

/* 
<div class="item-icon inline-flow rounded" style="background-image: url('<?= $this->record->getIcon() ?>')">
	&nbsp;
</div>
<div class="item-info inline-flow">
	<h2 class="text-large"><?= $this->recordLink($this->record, array("noTooltip" => true))?></h2>
	<p class="text-verysmall">is a</p>
	<p class="text-small"><a href="/class" rel="no-tooltip"><?= $this->record->_type ?></a></p>
	<p class="text-verysmall">available to the</p>
	<p class="text-small"><a href="#" rel="no-tooltip"><?= $this->record->faction ?></a></p>
</div>
*/