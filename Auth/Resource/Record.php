<?php
/**
 *
 *
 * undocumented class
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Auth_Resource_Record implements MW_Auth_Resource_Interface {

 public function getResourceName()
 {
	 return "record";
 }

 public function getResourceDescription()
 {
	 return "EpicDb Records";
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
				 'role' => array(MW_Auth_Group_Super::getInstance()),
			 ),
			array(
					'mode' => true,
					'role' => array(MW_Auth_Group_Guest::getInstance(), MW_Auth_Group_User::getInstance()),
					'privilege' => 'view'
				),
		 );
 }

 public function getRuntimePrivileges() {

 }
} // END class