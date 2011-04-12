<?php
/**
 * EpicDb_Mongo_Post_Question
 *
 * Question (Post) Mongo Object
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 **/
class EpicDb_Mongo_Post_Question extends EpicDb_Mongo_Post implements EpicDb_Vote_Interface_Votable
{
	protected static $_documentType = 'question';
	protected static $_editForm = 'EpicDb_Form_Post_Question';

	public function findAnswers($limit = 10, $query = array(), $sort = array()) {
		$query = array(
			"_parent" => $this->createReference(),
			'_deleted' => array(
					'$exists' => false
				)
		)+$query;
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
		)+$query;
		$sort = array("_created" => 1);
		return $results = EpicDb_Mongo::db('question-comment')->fetchAll($query, $sort, $limit);
	}

	public function countAnswers() {
		// Return a max of 9999
		return $this->findAnswers(9999)->count();
	}

} // END class EpicDb_Mongo_Post