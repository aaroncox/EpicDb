<?php
/**
 * EpicDb_Mongo_Record_Skill
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 **/
class EpicDb_Mongo_Post_Article extends EpicDb_Mongo_Post implements EpicDb_Vote_Interface_UpOnly
{
	protected static $_documentType = 'article';
  protected static $_editForm = 'EpicDb_Form_Post_Article';

	// Returns the string name of this
	public function getName() {
		if($this->title) {
			return $this->title;
		}
		return "";
	}
	
	public function getTooltipHelpers() {
		return array('icon', 'title', 'body');
	}
} // END class EpicDb_Mongo_Record_Skill