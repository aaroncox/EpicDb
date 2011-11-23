<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Form_Console extends EpicDb_Form
{
	public function process($data) {
		if($this->isValid($data)) {
			return $this->save($data);
		}
		return false;
	}
	public function render()
	{
		$this->removeDecorator('FloatClear');
		$this->getDecorator('HtmlTag')->setOption('class','r2-post-form')->setOption('id', 'ad-edit');
		return parent::render();
	}
} // END class EpicDb_Form_Console