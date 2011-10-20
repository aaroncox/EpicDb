<?php
class EpicDb_Validate_Timestamp extends Zend_Validate_Abstract
{

    const NOT_DATE = 'doesntExist';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_DATE => "This value is not a valid date."
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
		if(strtotime($value) == false) {
			$this->_error(self::NOT_DATE);
    	return false;
		}
		return true;
	}
}
