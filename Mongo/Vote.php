<?php
/**
 * EpicDb_Mongo_Record_Skill
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 **/
class EpicDb_Mongo_Vote extends MW_Mongo_Document
{
	protected static $_collectionName = 'votes';

	protected $_requirements = array(
		'post' => array('Document:EpicDb_Mongo_Post', 'AsReference'),
		'voter' => array('Document:EpicDb_Mongo_Profile_User', 'AsReference'),
		'target' => array('Document:EpicDb_Mongo_Profile', 'AsReference'),
		'vote' => array('Required'),
	);


	public function getPropertyClass($property, $data)
	{
		if ($property == 'target' || $property=='post' || $property =="voter") {
			return EpicDb_Mongo::dbClass($data['_type']);
		}
	}

	/**
	 * gets or makes a vote
	 *
	 * @return void
	 * @author Corey Frang
	 **/
	public static function getVote(EpicDb_Mongo_Profile_User $voter, EpicDb_Mongo_Post $post, $voteType, $createFlag = true)
	{
		$query = array(
				"post" => $post->createReference(),
				"vote" => $voteType,
		);
		if ($voter) {
			$query['voter'] = $voter->createReference();
		}
		$vote = static::fetchOne($query);
		if ($vote) return $vote;
		if ($createFlag) {
			$vote = new static();
			$vote->post = $post;
			$vote->voter = $voter;
			$vote->target = $post->tags->getTag('author');
			$vote->vote = $voteType;
			return $vote;
		}
		return false;
	}

	public static function getVoteByProfile($post, $profile) {
		if(!$profile instanceOf EpicDb_Mongo_Profile) return null;
		if(!$post instanceOf EpicDb_Mongo_Post) return null;
		if(!$post || !$profile) return null;
		$query = array(
				"post" => $post->createReference(),
				"voter" => $profile->createReference(),
				"vote" => array('$in' => array('up', 'down')),
			);
		return $current = static::fetchOne($query);
	}

	public static function getVoteSummary($post, $type = null) {
		$query = array(
				"post" => $post->createReference(),
			);
		if ($type) {
			if ($type == 'score') $query['type'] = array('$in'=>array('up','down'));
			else $query["type"] = $type;
		}
		$map = new MongoCode("function() {
			if (this.vote == 'up') emit('score', 1);
			if (this.vote == 'down') emit('score', -1);
			emit(this.vote, 1)
		}");
		$reduce = new MongoCode("function(key, values) {
			var sum = 0;
			for (var i in values) { sum += values[i]; }
			return sum;
		}");

		$db = static::getMongoDb();
		$result = $db->command(array(
				"mapreduce" => static::$_collectionName,
				"map" => $map,
				"reduce" => $reduce,
				"query" => $query,
		));

		$voteData = $db->selectCollection($result['result']);

		if ($type) {
			$data = $voteData->findOne(array('type'=>$type));
			
			$db->dropCollection($result['result']);
			if (!$data) return 0;
			return $data['value'];
		}

		$votes = array();
		$voteTally = $voteData->find();
		foreach ($voteTally as $voteData)
		{
			$type = $voteData['_id'];
			$count = $voteData['value'];
			$votes[$type] = $count;
		}
		$db->dropCollection($result['result']);

		return $votes;
	}
} // END class EpicDb_Mongo_Record_Skill
