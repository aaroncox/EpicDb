<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Form_Seed_Tag extends EpicDb_Form
{
	protected $_record = null;
	protected $_seed = null;

	public function getRecord()
	{
		if($this->_record) return $this->_record;
		throw new Exception("Record for this seed is undefined.");
	}

	public function setRecord($record)
	{
		if(!$record instanceOf EpicDb_Mongo_Record) {
			throw new Exception("This isn't an Record.");
		}
		$this->_record = $record;
		return $this;
	}
	
	public function getSeed()
	{
		if($this->_seed) return $this->_seed;
		throw new Exception("Seed not set for the form");
	}

	public function setSeed($seed)
	{
		$this->_seed = $seed;
		return $this;
	}

	public function getTagged() {
		$seed = $this->getSeed();
		$record = $this->getRecord();
		$tag = $seed->tag;
		$tagDb = $seed->tagDb;
		if($tagDb) {
			$query = array(
				'tags' => array(
					'$elemMatch' => array(
						'reason' => $tag,
						'ref' => $record->createReference(),
					)
				)
			);
			$results = EpicDb_Mongo::db($tagDb)->fetchAll($query);
			$tags = array();
			foreach($results as $result) {
				$tags[] = $result;
			}
		} elseif($tag) {
			$tags = $record->tags->getTags($tag); 
		} else {
			$tags = array();
		}
		return $tags;
	}
	
	public function setTagged() {
		$seed = $this->getSeed();
		$record = $this->getRecord();
		$tag = $seed->tag;
		$tagDb = $seed->tagDb;
		$value = $this->tags->getValue();
		$filter = new EpicDb_Filter_TagJSON();
		$value = $filter->toArray($value);
		if($tagDb) {
			$currentTags = $this->getTagged();
			$toSet = array();
			$isSet = array();
			foreach($value as $targetRecord) {
				$toSet[] = $targetRecord->_id."";
				// var_dump("Added", $targetRecord->_id."");
			}
			foreach($currentTags as $targetRecord) {
				$id = $targetRecord->_id."";
				if(in_array($id, $toSet)) {
					$isSet[] = $id;
					// var_dump("Skipping Current", $id);
					continue;
				}
				// var_dump("Untagging", $record, $tag);
				$targetRecord->tags->untag($record, $tag);
				$targetRecord->save();
			}
			foreach($value as $targetRecord) {
				if(in_array($targetRecord->_id."", $isSet)) {
					// var_dump("Skipping already added", $targetRecord->_id."");
					continue;
				}
				// var_dump("Tagging", $record, $tag);
				$targetRecord->tags->tag($record, $tag);
				$targetRecord->save();
			}
			// var_dump($this->getTagged()); exit;
		} elseif($tag) {
			$record->tags->setTags($tag, $value);
			$record->save();
		} else {
			// Some crazy shit here later
		}
	}

	public function init() {
		parent::init();
		$record = $this->getRecord();
		$seed = $this->getSeed();
		$this->addElement('tags', 'tags', array(
			'required' => true,
			'label' => 'Edit/Add tags as the answer.',
			'recordType' => $seed->tagType
		));
		$this->setDefaults(array(
			"tags" => $this->getTagged(),
		));
		$this->setButtons(array('save' => 'Save'));
	}
	
	public function process($data) {
		if($this->isValid($data)) {
			$this->setTagged();
			return true;
		}
		return false;
	}
} // END class EpicDb_Form_Seed_Tag