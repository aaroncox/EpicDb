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
  /**
   * The Privileges granted to group members
   *
   * @var array
   **/
  protected $_memberPrivileges = array("view", "post");

  // override requirements
  public function __construct($data = array(), $config = array()) {
    $this->addRequirements(array(

      '_owner' => array('Document:MW_Auth_Mongo_Role', 'AsReference', 'Required'),
      '_memberRole' => array('Document:MW_Auth_Mongo_Role', 'AsReference'),
      '_adminRole' => array('Document:MW_Auth_Mongo_Role', 'AsReference'),
      'admins' => array('DocumentSet'),
      'admins.$.profile' => array('Document:EpicDb_Mongo_Profile', 'AsReference'),		
      'members' => array('DocumentSet'),
      'members.$.profile' => array('Document:EpicDb_Mongo_Profile', 'AsReference'),
    ));
    return parent::__construct( $data, $config );
  }

  /**
   * getAdminRole - Returns the MW_Auth_Mongo_Role that represents the "admins"
   *
   * @return MW_Auth_Mongo_Role
   * @author Corey Frang
   **/
  public function getAdminRole()
  {
    if ( isset( $this->_adminRole ) ) {
      return $this->_adminRole;
    }
    $role = new MW_Auth_Mongo_Role( array(
      'type' => 'group',
      'groupName' => $this->name." admins",
      'groupDescription' => "Group administrators for group ".$this->id.": ".$this->name,
    ));

    // The Admin role is a member of the Members Role
    $role->roles->addDocument( $this->getMemberRole() );
    $role->save();

    // Grant all privileges on this item to the Admins
    $this->grant( $role );

    foreach( $this->admins as $admin ) {
      $user = $admin->profile->user;
      if ( $user ) {
        $user->addGroup( $role );
        $user->save();
      }
    }

    // Save this new role onto the record
    $this->_adminRole = $role;
    $this->save();
    return $role;
  }
  
  /**
   * getMemberRole() - Returns the MW_Auth_Mongo_Role that represents the "members" 
   *
   * @return void
   * @author Corey Frang
   **/
  public function getMemberRole() {
    if ( isset( $this->_memberRole ) ) {
      return $this->_memberRole;
    }
    $role = new MW_Auth_Mongo_Role( array(
      "type" => "group",
      "groupName" => $this->name." members",
      "groupDescription" => "Group Members for the group ".$this->name,
    ));
    $role->save();
    // Grant the member role privileges on this object
    $this->grant($role, $this->_memberPrivileges);

    // Retroactively grant membership to the members in the members documentset
    foreach( $this->members as $member ) {
      $user = $member->profile->user;
      if( $user ) {
        $user->addGroup($role); 
        $user->save();
      }
    }

    // Save this new role onto the record
    $this->_memberRole = $role;
    $this->save();
    return $role;
	}
	
} // END class EpicDb_Mongo_Profile_Group