<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Validate_Range extends Zend_Validate_Abstract
{	
	  const NOT_RANGE = 'notRange';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_RANGE => "Not acceptable range value."
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
		if(!$value instanceOf EpicDb_Mongo_Meta_Range) {
			$this->_error(self::NOT_RANGE);
    	return false;
		}
		if($value->min && $value->min > $value->max) {
			$this->_error(self::NOT_RANGE);
    	return false;
		}
		return true;
	}
} // END class EpicDb_Validate_Range