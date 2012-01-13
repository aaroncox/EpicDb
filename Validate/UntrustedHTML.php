<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Validate_UntrustedHTML extends Zend_Validate_Abstract
{
	const UNTRUSTED = 'untrusted';

	/**
	 * @var array
	 */
	protected $_messageTemplates = array(
		self::UNTRUSTED => "You must be Level 2 to link to more than one other page, or inline images",
	);

	protected $_element;
	public function __construct( EpicDb_Form_Element_Markdown $element )
	{
		$this->_element = $element;
	}

	/**
	 * isValid - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function isValid($value)
	{
		$this->_setValue($value);
		$rendered = $this->_element->getRenderedValue();
		if ( preg_match_all("/<a\W/i", $rendered, $matches, PREG_SET_ORDER) > 1 || 
			preg_match_all("/<img\W/i", $rendered,  $matches, PREG_SET_ORDER) > 0 )
		{
			$this->_error(self::UNTRUSTED);
			return false;
		}
		return true;
	}
} // END class EpicDb_Validate_Range