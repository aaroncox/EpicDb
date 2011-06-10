<?php
/**
 *
 *
 * @author Corey Frang
 * @package R2Db_Mongo
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 */

/**
 *  R2Db_Mongo_Revision
 *
 * undocumented
 *
 * @author Corey Frang
 * @package R2Db_Mongo
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 * @version $Id: Revision.php 699 2011-03-19 01:56:51Z root $
 */
class EpicDb_Mongo_Revision extends EpicDb_Mongo_Document {
	protected $_requirements = array(
		'_lastEditedBy' => array('Document:EpicDb_Mongo_Profile', 'AsReference'),
	);

	public static function makeEditFor(EpicDb_Interface_Revisionable $thing, $reason = null) {
		$me = EpicDb_Auth::getInstance()->getUserProfile();

		$editingWindow = 
			$thing->_lastEditedBy &&
			$thing->_lastEditedBy->_id == $me->_id &&
			$thing->_lastEdited > ( time() - ( 5 * 60 ) );

		if(!$thing->_isNew) {
			// should check for recent revisions by the same person first...
			if ( !$editingWindow ) {
				$thing->newRevision();
			}
			$thing->_lastEditedReason = $reason;
		}
		if ( !$editingWindow ) $thing->_lastEdited = time();
		$thing->_lastEditedBy = $me;
	}
}