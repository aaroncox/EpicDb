<?php
/**
 * EpicDb_Mongo_Post_Question
 *
 * Question (Post) Mongo Object
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 **/
class EpicDb_Mongo_Post_Question extends EpicDb_Mongo_Post
{
	protected static $_collectionName = 'posts';
  protected static $_documentType = 'question';
	protected static $_editForm = 'EpicDb_Form_Post_Question';

	/**
	 * __construct - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function __construct($data = array(), $config = array())
	{
		// $this->addRequirements(array(
		// 	'revisions' => array('DocumentSet'),
		// 	'revisions.$' => array('Document:EpicDb_Mongo_Revision'),
		// ));
		return parent::__construct($data, $config);
	}
	
	public function findAnswers($limit = 10, $query = array(), $sort = array()) {
		$query = array(
			"_parent" => $this->createReference(),
			'_deleted' => array(
					'$exists' => false
				)
		);
		$sort = array("_created" => 1);
		return $results = EpicDb_Mongo::db('answer')->fetchAll($query, $sort, $limit);
	}
	
	public function findComments($limit = 10, $query = array(), $sort = array()) {
		// var_dump($this->createReference()); 
		$query = array(
			"_parent" => $this->createReference(),
			'_deleted' => array(
					'$exists' => false
				)
		);
		$sort = array("_created" => 1);
		return $results = EpicDb_Mongo::db('comment')->fetchAll($query, $sort, $limit);		
	}
	
	public function countAnswers() {
		// Return a max of 9999
		return $this->findAnswers(9999)->count(); 
	}
	
} // END class EpicDb_Mongo_Post