<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_View_Helper_SeedStub extends EpicDb_View_Helper_PostStub
{
	public function renderControls() {
		$html = " ";
		$seed = $this->_seed;
		$record = $this->_record;
		$user = EpicDb_Auth::getInstance()->getUser();
		if($user && MW_Auth::getInstance()->hasPrivilege(new MW_Auth_Resource_Super(), 'answer-seed')) {
			$html .= $this->view->button(array(
				'controller'=>'record',
				'action'=>'seed',
				'record'=>$record,
				'seed'=>$seed,
			), 'seed-question', true, array(
				'text' => 'View the Full Question',
				'icon' => 'help'
			));
		} else {
			$html .= $this->view->seedLink($seed, $record, array("text" => "View Original"));
		}
		return $this->htmlTag("div", array("class" => "seed-controls"), $html);
	}
	public function renderBody() {
		$html = " ";
		$record = $this->_record;
		$seed = $this->_seed;
		$db = $seed->tagDb;
		$tag = $seed->tag;
		if($db) {
			$collection = EpicDb_Mongo::db($db);
			$query = array(
				'tags' => array(
					'$elemMatch' => array(
						'ref' => $record->createReference(),
						'reason' => $tag,
					)
				)
			);
			$answers = $collection->fetchAll($query);
		} elseif($tag) {
			$answers = $record->tags->getTags($tag);
		} else {
			$answers = array();
		}
		// Load in specific answer thing here.
		foreach($answers as $answer) {
			$html .= $this->view->htmlTag("div", array("style" => "width: 180px", "class" => "inline-flow"), $this->view->card($answer, array('class' => 'medium-icon')));
		}
		$html .= $seed->wiki($record)->html;
		return $html;
	}
	public function seedStub($seed, $record) {
		$this->_seed = $seed;
		$this->_record = $record;
		$body = $this->renderBody();
		return $this->htmlTag("div", array("class" => "seed-stub padded-10a", "id" => "seed-".$seed->id), 
			$this->htmlTag("div", array("class" => "seed-header"), 
				$this->htmlTag("img", array("src" => "/images/element/message-collapse.png"))."".
				$this->htmlTag("h3", array("class" => "inline-flow text-large"), $seed->renderTitle($record))
			)."".
			$this->htmlTag("div", array("class" => "seed-body"), 
				$body."".
				$this->renderControls()
			)
		);
	}
} // END class EpicDb_View_Helper_SeedStub extends EpicDb_View_Helper_PostStub