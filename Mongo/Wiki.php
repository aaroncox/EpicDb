<?php
/**
 * EpicDb_Mongo_Wiki
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 **/
class EpicDb_Mongo_Wiki extends MW_Auth_Mongo_Resource_Document
{
  protected static $_collectionName = 'wiki';
	
	protected $_requirements = array(
		'record' => array('AsReference'),
		'type' => array('Required'),
  );

	public static function get($record, $type) {
		$query = array(
			'record' => $record->createReference(),
			'type' => $type,
		);
		return static::fetchOne($query);
	}
	
	public function getPropertyClass($property, $data)
	{
	  if ($property == 'record') {
	    return EpicDb_Mongo::dbClass($data['_type']);
	  }
	}
}