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
class EpicDb_Auth_Resource_Post implements MW_Auth_Resource_Interface {
 
 public function getResourceName()
 {
   return "post";
 }
 
 public function getResourceDescription()
 {
   return "EpicDb Posts";
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
			array(
					'mode' => true,
					'role' => array(MW_Auth_Group_User::getInstance()),
					'privilege' => array('use', 'create'),
				),
     );      
 }
 
 public function getRuntimePrivileges() {
   
 }
} // END class