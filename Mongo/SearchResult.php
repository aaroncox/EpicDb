<?php
/**
 * EpicDb_Mongo_Record
 *
 * undocumented class
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_SearchResult extends MW_Mongo_Document
{
	protected static $_collectionName = 'search';
	protected static $_documentType = null;
	
	protected $_requirements = array(
		'records' => array('DocumentSet:EpicDb_Mongo_Tags'),
		'tags' => array('DocumentSet:EpicDb_Mongo_Tags'),
  );

	public function generate($data) {
		$query = array();
		foreach($data['records'] as $record) {
			$query['$and'][] = array(
				'records' => array(
					'$elemMatch' => array(
						'ref' => $record->createReference()
					)
				)
			);			
		}
		$result = $this->fetchOne($query);
		if(!$result) {
			$result = EpicDb_Mongo::newDoc('search');
			$result->records->setTags('records', $data['records']);
		}
		$result->type = $data['type'];
		$result->keywords = EpicDb_Search::getInstance()->keywordExplode($data['keywords']);;
		foreach(array('name', 'url', 'score', 'icon') as $key) {
			if(isset($data[$key])) {
				$result->$key = $data[$key];
			}
		}
		$result->tags->setFromArray($data['tags']);
		$result->lastUpdated = time();
		$result->save();
	}
}