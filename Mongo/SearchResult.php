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
		'record' => array('AsReference'),
		'seed' => array('AsReference'),
		'tags' => array('DocumentSet:EpicDb_Mongo_Tags'),
  );
}