<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Validate_Damage extends Zend_Validate_Abstract
{	
	  const NOT_DAMAGE = 'notDamage';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_DAMAGE => "Not acceptable damage value."
    );

	/**
	 * isValid - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function isValid($value)
	{
		$this->_setValue($value);
		if(!$value instanceOf EpicDb_Mongo_Meta_Damage) {
			$this->_error(self::NOT_DAMAGE);
    	return false;
		}
		if($value->min && $value->min > $value->max) {
			$this->_error(self::NOT_DAMAGE);
    	return false;
		}
		return true;
	}
} // END class EpicDb_Validate_Damage