<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Validate_PostLimits extends Zend_Validate_Abstract
{
	const OVER_LIMIT = 'overLimit';

	/**
	 * @var array
	 */
	protected $_messageTemplates = array(
		self::OVER_LIMIT => "Over Post Limit - Please wait %value% seconds before posting again",
	);

	protected function _getLimits() {
		$user = EpicDb_Auth::getInstance()->getUserProfile();
		if ( !$user ) {
			return false;
		}
		if ( EpicDb_Auth::getInstance()->hasPrivilege( new EpicDb_Auth_Resource_Moderator() ) ) {
			return false;
		}
		$level = $user->getLevel();
		if ( $level < 2 ) {
			// one post, 60 seconds
			return array( 1, 90 );
		}
		if ( $level < 5 ) {
			// three posts, 90 seconds
			return array( 3, 90 );
		}
		if ( $level < 15 ) {
			return array( 5, 120 );
		}
		if ( $level < 25 ) {
			return array( 7, 150 );
		}
		if ( $level < 35 ) {
			return array( 9, 180 );
		}
		if ( $level < 45 ) {
			return array( 11, 210 );
		}
	}

	/**
	 * isValid - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function isValid($value)
	{
		$limits = $this->_getLimits();
		if ( !$limits ) { 
			return true;
		}
		$query = array();
		$query['tags']['$elemMatch'] = array(
			'ref' => EpicDb_Auth::getInstance()->getUserProfile()->createReference(),
			'reason' => "author"
		);
		$query['_created']['$gt'] = time() - $limits[1];
		$recentPosts = EpicDb_Mongo::db( "post" )->fetchAll( $query, array("_created" => 1) );
		if ( count( $recentPosts ) >= $limits[0] ) {
			foreach( $recentPosts as $post ) {
				$this->_setValue( $limits[1] - time() + $post->_created );
				break;
			}
			$this->_error(self::OVER_LIMIT);
			return false;
		}
		return true;
	}
} // END class EpicDb_Validate_Damage