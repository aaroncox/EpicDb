<?php
/**
 * EpicDb_Mongo_Profile_User
 *
 * undocumented class
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_Profile_User extends EpicDb_Mongo_Profile
{
	protected static $_documentType = 'user';
	protected static $_editForm = 'EpicDb_Form_Profile_User';

	public function __construct($data = array(), $config = array())
	{
		// handled here in order to deal with subclassed resources...
		if (!is_array($this->_requirements)) $this->_requirements = array();
		$this->_requirements += array(
			'user' => array('Document:MW_Auth_Mongo_User', 'AsReference', 'Required'),
			'watching' => array('DocumentSet:EpicDb_Mongo_DocumentSet_Dynamic'),
			'watching.$' => array('Document:MW_Mongo_Document', 'AsReference', 'Required'),
			'following' => array('DocumentSet:EpicDb_Mongo_DocumentSet_Dynamic'),
			'following.$' => array('Document:MW_Mongo_Document', 'AsReference', 'Required'),
			'blocking' => array('DocumentSet:EpicDb_Mongo_DocumentSet_Dynamic'),
			'blocking.$' => array('Document:MW_Mongo_Document', 'AsReference', 'Required'),
			);
		$return = parent::__construct($data, $config);
	}

	public function getTooltipHelpers() {
		$return = array("icon", "name");
		if($this->reputation) {
			$return[] = 'level';
		}
		$return[] = "link";
		$return[] = "limitDescription";
		return $return;
	}

	public function getDescription() {
		if($this->bio) return $this->bio;
		return '';
	}

	public function isWatching(MW_Mongo_Document $record)
	{
		$id = (string) $record->_id;
		foreach ($this->watching as $key => $target) {
	// var_dump("-------------", $id, $key, $target);
			if ( !$target ) continue;
			if ( (string) $target->_id == $id) {
				return true;
			}
		}
		return false;
	}

	public function watch($record)
	{
		// // var_dump($record); exit;
		//     if (!$this->isWatching($record)) {
		//       $this->watching->addDocument($record);
		// }
		// var_dump($record); exit;
		return $this;
	}

	public function unwatch($record)
	{
		// $id = (string)$record->_id;
		// foreach ($this->watching as $key => $target) {
		//   if ((string)$target->_id == $id) {
		//     $this->watching->setProperty($key, null);
		//   }
		// }
		return $this;
	}

	public function isFollowing(MW_Mongo_Document $record)
	{
		$id = (string) $record->_id;
		foreach ($this->following as $key => $target) {
			if ( (string) $target->_id == $id) {
				return true;
			}
		}
		return false;
	}

	public function follow($record)
	{
		if (!$this->isFollowing($record))
			$this->following->addDocument($record);
		return $this;
	}

	public function unfollow($record)
	{
		$id = (string)$record->_id;
		foreach ($this->following as $key => $target) {
			if ((string)$target->_id == $id) {
				$this->following->setProperty($key, null);
			}
		}
		return $this;
	}

	public function isBlocking(MW_Mongo_Document $record)
	{
		$id = (string) $record->_id;
		foreach ($this->blocking as $key => $target) {
			if ( (string) $target->_id == $id) {
				return true;
			}
		}
		return false;
	}

	public function block($record)
	{
		if (!$this->isBlocking($record))
			$this->blocking->addDocument($record);
		return $this;
	}

	public function unblock($record)
	{
		$id = (string)$record->_id;
		foreach ($this->blocking as $key => $target) {
			if ((string)$target->_id == $id) {
				$this->blocking->setProperty($key, null);
			}
		}
		return $this;
	}


	public static function getProfile($user) {
		if(!$user) return null;
		if($user instanceOf EpicDb_Mongo_Profile_User) {
			return $user;
		}
		$query = array();
		if($user instanceOf MW_Auth_Mongo_Role) {
			$query['user'] = $user->createReference();
		} elseif(is_string($user)) {
			$query = array("name" => new MongoRegex('/'.$user.'/i'));
			$user = false;
		}
		$profile = self::fetchOne($query, true);
		// var_dump($query, $profile, $user); exit;
		if(!$profile && $user) {
			$slug = new MW_Filter_Slug();
			$profile = new self();
			$profile->user = $user;
			$profile->slug = $slug->filter($user->name);
			$profile->username = strtolower($slug->filter($user->name));
			$profile->name = $slug->filter($user->name);
			$profile->display_email = strtolower($slug->filter($user->email));
			$profile->grant($user);
			// var_dump($user); exit;
			$profile->save();
		}
		return $profile;
	}

	public function getIcon() {
		if($icon = $this->tags->getTag('icon')) {
			return $icon->getIcon();
		}
		if($this->email) {
			$helper = new EpicDb_View_Helper_Gravatar();
			return $helper->gravatar($this->email)->url();
		}
		return "http://s3.r2-db.com/unknown.jpg";
	}

	public function getMemberships($type = 'profile') {
		$query = array();
		$query['$or'][] = array('members' => $this->createReference());
		$query['$or'][] = array('admins' => $this->createReference());
		return EpicDb_Mongo::db($type)->fetchAll($query);
	}

	public function getAuthoredPosts($query = array(), $sort = array("_created" => -1), $limit = false) {
		$query['$and'][] = array(
			'tags.ref' => $this->createReference(),
			'tags.reason' => 'author'
		);
		return EpicDb_Mongo::db('post')->fetchAll($query, $sort, $limit);
	}

	public function getLevel()
	{
		$level = floor(sqrt($this->reputation/8));
		$level = min( 50, max( 1, $level ) );
		return $level;
	}

} // END class EpicDb_Mongo_Profile_User