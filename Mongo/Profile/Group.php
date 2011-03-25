<?php
/**
 * EpicDb_Mongo_Profile_Group
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_Profile_Group extends EpicDb_Mongo_Profile
{
	// This code needs to be improved upon, it's just fixing a crash atm. Should groups have walls? Should all profiles get walls?
	public function getWalls() {
		return array();
	}
} // END class EpicDb_Mongo_Profile_Group