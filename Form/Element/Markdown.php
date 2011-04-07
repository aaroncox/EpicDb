<?php
/**
 * R2Db_Form_Element_Markdown
 *
 * undocumented class
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Form_Element_Markdown extends Zend_Form_Element_Textarea {
	public $helper = 'markitup';

	protected $_editor;

	public function __construct($spec, $options = null)
	{
			if (is_string($spec) && ((null !== $options) && is_string($options))) {
					$options = array('label' => $options);
			}
			if (isset($options['class'])) $options['class'].=' markdown';
			else $options['class'] = 'markdown';
			parent::__construct($spec, $options);
	}

	public function getRenderedValue()
	{
		$source = $this->getValue();
		$markdown = new EpicDb_Markup_Markdown();
		$html = $markdown->render($source);
		$purifier = new MW_Filter_HtmlPurifier(array(array("HTML.Nofollow", 1)));
		return $purifier->filter($html);
	}
} // END class R2Db_Form_Element_Markdown