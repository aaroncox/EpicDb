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
		return $this->findResponses( $limit, array( "_type" => "answer" ) + $query, $sort );
	}

	public function findComments($limit = 10, $query = array(), $sort = array()) {
		return $this->findResponses( $limit, array( "_type" => "question-comment" ) + $query, $sort );
	}

	public function countAnswers() {
		// TODO - XHProf Improvement Here: This count repeatedly fires, could be improved and reduce pageload by approx 1/2 second
		// Return a max of 9999
		return $this->findAnswers( false )->count();
	}

} // END class EpicDb_Mongo_Post