<?php
/**
 * Momentum Workshop
 *
 * @author Corey Frang
 * @category MW
 * @package MW_Auth
 * @subpackage Form
 * @copyright Copyright (c) 2010 Momentum Workshop, Inc
 */

/**
 *  MW_Auth_Form_Validate_Username
 *
 * undocumented
 *
 * @author Corey Frang
 * @category MW
 * @package MW_Auth
 * @subpackage Form
 * @copyright Copyright (c) 2010 Momentum Workshop, Inc
 * @version $Id: Username.php 310 2010-02-12 20:08:26Z corey $
 */
class EpicDb_Auth_Form_Validate_Username extends Zend_Validate_Abstract {
	const NOT_IN_USE = 'notInUse';
	const IN_USE = 'inUse';
	/**
	 * Error messages
	 * @var array
	 */
	protected $_messageTemplates = array(
		self::NOT_IN_USE => 'Congratulations! This username is available.',
		self::IN_USE => 'The username you have entered is already taken.',
	);

	public function isValid($value)
	{
			$this->_setValue($value);
			// var_dump(EpicDb_Mongo_Profile_User::fetchOne(array('username' => $value))); exit;
			if (!EpicDb_Mongo::db('user')->fetchOne(array('username' => $value))) {
				return true;
			}
			$this->_error(self::IN_USE);
			return false;
	}
}