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
 *  EpicDb_Auth_Resource_Moderator
 *
 * undocumented
 *
 * @author Corey Frang
 * @package EpicDb_Auth
 * @subpackage Resource
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 * @version $Id:$
 */
class EpicDb_Auth_Resource_Moderator implements MW_Auth_Resource_Interface {

 public function getResourceName()
 {
	 return "moderator";
 }

 public function getResourceDescription()
 {
	 return "Moderator Resources";
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
				 'role' => array(MW_Auth_Group_Super::getInstance(), EpicDb_Auth_Group_Moderators::getInstance()),
			 ),
		 );
 }

 public function getRuntimePrivileges() {

 }}