<?php
/**
 *
 *
 * @author Corey Frang
 * @package EpicDb_Mongo
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 */

/**
 *  EpicDb_Mongo_Votes
 *
 * undocumented
 *
 * @author Corey Frang
 * @package EpicDb_Mongo
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 * @version $Id:$
 */
class EpicDb_Mongo_Votes extends Shanty_Mongo_DocumentSet {
	public function getPropertyClass( $idx, $data )
	{
		return "EpicDb_Mongo_Vote";
	}
	public function getProperty( $idx ) {
		return EpicDb_Vote::fromMongo( parent::getProperty($idx) );
	}
}