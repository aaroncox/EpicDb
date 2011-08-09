<?php
/**
 * EpicDb_Mongo_Wiki
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 **/
class EpicDb_Mongo_Wiki extends EpicDb_Auth_Mongo_Resource_Document implements EpicDb_Interface_Revisionable
{
  protected static $_collectionName = 'wiki';
	
	protected $_requirements = array(
		'record' => array('AsReference'),
		'type' => array('Required'),
		'_lastEditedBy' => array('Document:EpicDb_Mongo_Profile', 'AsReference'),
		'revisions' => array('DocumentSet'),
		'revisions.$' => array('Document:EpicDb_Mongo_Revision'),
		'revisions.$.tags' => array('DocumentSet:EpicDb_Mongo_Tags'),
  );

	public static function get($record, $type, $createFlag = true) {
		$query = array(
			'record' => $record->createReference(),
			'type' => $type,
		);
		$return = static::fetchOne($query);
		if(!$return && $createFlag) {
			$return = new static();
			$return->type = $type;
			$return->record = $record;
		}
		return $return;
	}
	
	public function getPropertyClass($property, $data)
	{
	  if ($property == 'record') {
	    return EpicDb_Mongo::dbClass($data['_type']);
	  }
	}
	
	public function newRevision()
	{
		$revision = $this->revisions->new();
		$this->revisions->addDocument($revision);
		$copy = array('source', 'header', '_lastEditedBy', '_lastEditedReason', '_lastEdited');
		foreach ($copy as $key) {
			$revision->$key = $this->$key;
		}
	}
}