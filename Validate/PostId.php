<?php
class EpicDb_Validate_PostId extends Zend_Validate_Abstract
{

    const DOESNT_EXISTS = 'doesntExist';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::DOESNT_EXISTS => "Cannot find post."
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
		if(!EpicDb_Mongo::db('posts')->find(new MongoId($value))) {
			$this->_error(self::DOESNT_EXISTS);
    	return false;
		}
		return true;
	}
}
