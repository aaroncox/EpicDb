<?php
/**
 * EpicDb_Mongo_Post_Question
 *
 * Question (Post) Mongo Object
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 **/
class EpicDb_Mongo_Post_Question extends EpicDb_Mongo_Post implements EpicDb_Vote_Interface_Votable, EpicDb_Interface_Autotweet, EpicDb_Vote_Interface_Closable
{
	public $routeName = "questions";
	protected static $_documentType = 'question';
	protected static $_editForm = 'EpicDb_Form_Post_Question';

	public function __construct($data = array(), $config = array())
	{
		$this->addRequirements(array(
			'dupeOf' => array( 'Document:EpicDb_Mongo_Post_Question', 'AsReference' ),
		));
		return parent::__construct($data, $config);
	}

	public function getPropertyClass($property, $data) {
		if ($property == "dupeOf" && isset($data['_type'])) {
			return EpicDb_Mongo::dbClass($data['_type']);
		}
	}

	public function findAnswers($query = array(), $sort = array('votes.accept' => -1, 'votes.score' => -1), $limit = false) {
		return $this->findResponses(array( "_type" => "answer" ) + $query, $sort, $limit);
	}

	public function findComments($query = array(), $sort = array(), $limit = false) {
		return $this->findResponses( array( "_type" => "question-comment" ) + $query, $sort, $limit );
	}

	public function countAnswers() {
		// TODO - XHProf Improvement Here: This count repeatedly fires, could be improved and reduce pageload by approx 1/2 second
		// Return a max of 9999
		return $this->findAnswers( array('_deleted' => array('$exists' => false) ) )->count();
	}
	
	public function getRouteParams() {
		$filter = new MW_Filter_Slug();
		return parent::getRouteParams()+array('slug' => $filter->filter($this->title));
	}
		
	public function save() {
		$this->_answerCount = $this->countAnswers();
		return parent::save();
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

	public function close($profiles, $reason, $dupe = false)
	{
		$this->closed = time();
		$this->closedReason = $reason;
		if ( $dupe ) {
			$this->dupeOf = $dupe;
		}
		$this->tags->setTags("closed-by", $profiles);
		$this->tags->setTags("reopend-by", array());
		$this->save();
		foreach($this->findAnswers() as $answer) {
			$answer->save();
		}
	}

	public function reopen($profiles)
	{
		$this->closed = null;
		$this->closedReason = null;
		$this->dupeOf = null;
		$this->tags->setTags("closed-by", array());
		$this->tags->setTags("reopend-by", $profiles);
		$this->save();
		foreach($this->findAnswers() as $answer) {
			$answer->save();
		}
	}

	public function getName() {
		$title = parent::getName();
		if($this->closed) $title .= " [closed]";
		return $title;
	}
	

} // END class EpicDb_Mongo_Post