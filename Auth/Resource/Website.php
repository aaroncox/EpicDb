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
class EpicDb_Auth_Resource_Website extends EpicDb_Auth_Resource_Profile {
 
 public function getResourceName()
 {
   return "website";
 }
 
 public function getResourceDescription()
 {
   return "EpicDb Websites";
 }
 
 public function getParentResource()
 {
   return new EpicDb_Auth_Resource_Profile();
 }
 
 public function getRuntimePrivileges() {
   
 }
} // END class