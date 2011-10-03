<?php
/**
 * EpicDb_Mongo_Post_Question
 *
 * Question (Post) Mongo Object
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 **/
class EpicDb_Mongo_Post_Question extends EpicDb_Mongo_Post implements EpicDb_Vote_Interface_Votable, EpicDb_Interface_Autotweet
{
	public $routeName = "questions";
	protected static $_documentType = 'question';
	protected static $_editForm = 'EpicDb_Form_Post_Question';

	public function findAnswers($limit = 10, $query = array(), $sort = array('votes.accept' => -1, 'votes.score' => -1)) {
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
	
	public function getRouteParams() {
		$filter = new MW_Filter_Slug();
		return parent::getRouteParams()+array('slug' => $filter->filter($this->title));
	}
	
	public function autoTweet() {
		if(!$this->_twitterId && $this->votes['score'] >= 2) {
			// There's gotta be a better way to do this... 
			$rtwoqa = EpicDb_Mongo::db('user')->fetchOne(array('id' => 2732));
			try {
				$token = unserialize($rtwoqa->user->auth[0]->twitter->oauth_token);
				$twitter = new Zend_Service_Twitter(array(
				    'username' => 'rtwoqa',
				    'accessToken' => $token
				));
				// Build the new tweet.
				$link = "http://r2db.com/question/".$this->id;
				$newTweet = substr($this->title, 0, 100)." ".$link." #SWTOR";
				// Send the new tweet
				$response = $twitter->status->update($newTweet);	
				// Set that this was tweeted and what the twitter ID was.
				$this->_twitterId = new MongoInt64($response->id);
				$this->save();				
			} catch (Exception $e) {
				// Couldn't tweet for some reason.
			}
		}
	}
	

} // END class EpicDb_Mongo_Post