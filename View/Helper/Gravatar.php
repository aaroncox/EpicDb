<?php
/**
 * EpicDb_View_Helper_Gravatar
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_Gravatar extends Zend_View_Helper_Gravatar
{
	protected $_defaultOptions = array(
		// 'defaultImg' => 'http://s3.r2-db.com/unknown.jpg',
		'defaultImg' => 'identicon',
		'rating' => 'pg',
	);
	public function gravatar($email = "", $options = array(), $attribs = array()) {
		$options += $this->_defaultOptions;
		if (empty($attribs['alt'])) $attribs['alt'] = 'user gravatar';
		return parent::gravatar($email, $options, $attribs);
	}
	public function url() {
		return $this->_getAvatarUrl();
	}
} // END class EpicDb_View_Helper_Gravatar