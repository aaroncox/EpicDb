<?php

class EpicDb_View_Helper_Markitup extends Zend_View_Helper_FormTextarea {
  public function markitup($name, $value = null, array $attribs = null, $options = null, $listsep = "<br />\n")
  {
	  $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
    extract($info); // name, value, attribs, options, listsep, disable
	  if (isset($id)) {
        if (isset($attribs) && is_array($attribs)) {
            $attribs['id'] = $id;
        } else {
            $attribs = array('id' => $id);
        }
    }
    if ($options && $options[$value]) $value = $options[$value];
		return $this->formTextarea($name, $value, $attribs);
  }
} // END class LQA_View_Helper_PlayerScorecard