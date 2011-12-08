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
class EpicDb_Auth_Resource_RepVotes implements MW_Auth_Resource_Interface {

public function getResourceName()
 {
	return "repvotes";
 }

public function getResourceDescription()
 {
	return "Reputation Based Votes";
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
				'privilege' => 'up',
				'role' => array( EpicDb_Auth_Group_UpVoters::getInstance(), EpicDb_Auth_Group_Moderators::getInstance() )
			),
			array(
				'mode' => true,
				'privilege' => 'down',
				'role' => array( EpicDb_Auth_Group_DownVoters::getInstance(), EpicDb_Auth_Group_Moderators::getInstance() )
			),
		);
 }

public function getRuntimePrivileges() {

 }}