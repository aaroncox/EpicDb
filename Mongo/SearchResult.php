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
		'keywords' => array('Array'),
  );

	public function generate($data) {
		$query = array();
		// Specific Result types for records
		if(isset($data['subtype'])) {
			$query['subtype'] = $data['subtype'];
		} else {
			$data['subtype'] = $query['subtype'] = 'base';
		}
		// This whole wrapping shit sucks, it could be a lot better, but I am le' tired. FIRE ZE MISSLEZ!
		if(isset($data['wrap'])) {
			if(isset($data['quality'])) {
				$replace = "<span class='quality-".$data['quality']."'>".$data['name']."</span>";				
			} else {
				$replace = "<span class='gold-text'>".$data['name']."</span>";
			}
			$data['name'] = str_replace("###", $replace, $data['wrap']);			
		} else {
			if(isset($data['quality'])) {
				$data['name'] = "<span class='quality-".$data['quality']."'>".$data['name']."</span>";				
			} else {
				$data['name'] = "<span class='gold-text'>".$data['name']."</span>";								
			}
		}
		// Torhead Branding
		if(isset($data['torhead'])) {
			// $data['url'] = $data['torhead'];				
			$data['name'] = "<img src='/images/torhead-arrow.png' class='torhead-link'/>".$data['name'];
			if(isset($data['score'])) {
				$data['score'] = $data['score'] - 1;
			}
		}
		foreach($data['records'] as $record) {
			$query['type'] = $data['type'];
			$query['$and'][] = array(
				'records' => array(
					'$elemMatch' => array(
						'ref' => $record->createReference()
					)
				)
			);			
		}
		$result = $this->fetchOne($query);
		// var_dump($query, $result); exit;
		if(!$result) {
			$result = EpicDb_Mongo::newDoc('search');
			$result->records->setTags('records', $data['records']);
		}
		$result->type = $data['type'];
		$keywords = EpicDb_Search::getInstance()->keywordExplode($data['keywords']);
		if(isset($data['hints'])) {
			$keywords = array_merge($keywords, $data['hints']);
		}
		$result->keywords = $keywords;
		foreach(array('name', 'url', 'score', 'icon', 'subtype') as $key) {
			if(isset($data[$key])) {
				$result->$key = $data[$key];
			}
		}
		$result->tags->setFromArray($data['tags']);
		$result->lastUpdated = time();
		// var_dump($result->type, $result->subtype, $result->name, "-------"); 
		$result->save();
	}
}