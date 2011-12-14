<?php
/**
 *
 *
 * @author Corey Frang
 * @package EpicDb_Auth
 * @subpackage Resource
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 */

/**
 *  EpicDb_Auth_Resource_RepVotes
 *
 * undocumented
 *
 * @author Corey Frang
 * @package EpicDb_Auth
 * @subpackage Resource
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 * @version $Id:$
 */
class EpicDb_Auth_Resource_Vote implements MW_Auth_Resource_Interface {

public function getResourceName()
 {
	return "vote";
 }

public function getResourceDescription()
 {
	return "Voting System";
 }

public function getParentResource()
 {
	return null;
 }

public function getDefaultPrivileges()
 {
	return array(
			array(
				'mode' => true,
				'privilege' => 'flag',
				'role' => array( EpicDb_Auth_Group_Flaggers::getInstance(), EpicDb_Auth_Group_Moderators::getInstance() )
			),
		);
 }

public function getRuntimePrivileges() {

 }}