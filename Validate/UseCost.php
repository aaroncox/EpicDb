<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Validate_UseCost extends Zend_Validate_Abstract
{	
	  const NOT_USE_COST = 'notUseCost';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_USE_COST => "Not acceptable use cost. Example: {'type':'force','value':3}"
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
		if(!isset($value['type'])) {
			$this->_error(self::NOT_USE_COST);
    	return false;
		}
		if(!isset($value['value']) && (int) $value['value'] <= 0) {
			$this->_error(self::NOT_USE_COST);
    	return false;
		}
		return true;
	}
} // END class EpicDb_Validate_Range