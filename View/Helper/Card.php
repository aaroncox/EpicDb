<?php
/**
 * EpicDb_View_Helper_Card
 *
 * Builds the "card" version of a record
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_Card extends MW_View_Helper_HtmlTag
{
	// public function profileCard(EpicDb_Mongo_Profile $profile) {
	// 	return 
	// }
	public function link($record, $params = array()) {
		if($record instanceOf EpicDb_Mongo_Profile) {
			return $this->view->profileLink($record, $params);
		}
		return $this->view->recordLink($record, $params);
	}
	public function addExtra($extra) {
		return $this->htmlTag("p", array("class" => 'text-small'), $extra);
	}
	protected function _detail($qualifier, $content) {
		if(!$qualifier || !$content) return '';
		return $this->htmlTag("p", array('class' => 'text-verysmall'), $qualifier)."".
			$this->htmlTag("p", array('class' => 'text-small'), $content);		
	}
	public function getIcon($record) {
		if($record->email) {
			return $this->view->gravatar($record->email)->url();
		}
		return $record->getIcon();
	}
	public function cardDetails($record, $params = null) {
		$details = '';
		$details .= $this->htmlTag("h2", array('class' => 'text-large'), $this->link($record));
		if(isset($params['content'])) {
			foreach($params['content'] as $qualifier => $content) {
				$details .= $this->_detail($qualifier, $content);
			}						
		} else {
			foreach($record->cardProperties($this->view) as $qualifier => $content) {
				$details .= $this->_detail($qualifier, $content);
			}			
		}
		if(isset($params['extra'])) $details .= $this->addExtra($params['extra']);
		return $details;
	}
	public function cardScore($record) {
		if(!$record->reputation) return '';
		$user = $record->user; 
		$admin = $moderator = null;
		if($user instanceOf MW_Auth_Mongo_Role) {
			$admin = $user->isMember(MW_Auth_Group_Super::getInstance());									
			// if (!$admin) {
			// 	$moderator = $user->isMember(R2Db_Auth_Group_Moderators::getInstance());	
			// }
		}	
		$title = '';
		if($admin || $moderator) {
			$title .= $this->htmlTag("span", array("title" => $admin?"Site Administrator":"Librarian"), $admin ? "&diams;&diams;" : "&diams;");
		}	
		return $this->htmlTag("div", array("class" => "record-score"), 
			$title."".$record->reputation
		);
	}
	public function unknownCard($params) {
		return $this->htmlTag("div", array('class' => 'inline-flow db-card transparent-bg rounded '.$params['class']), 
			$this->htmlTag("div", array('class' => 'record-icon inline-flow rounded'), 
				$this->htmlTag("img", array('src' => '/images/icons/unknown.jpg'))
			)."".
			$this->htmlTag("div", array('class' => 'record-info inline-flow'), $this->htmlTag("h2", array('class' => 'text-large'), 'Missing Account'))
		);		
	}
	public function card($record, $params = array()) {
		if(!$record) return $this->unknownCard($params);
		if(!isset($params['extra'])) $params['extra'] = null;
		if(!isset($params['class'])) $params['class'] = null;
		if($record->characters && $character = $record->characters->getPrimary()) {
			$params['class'] .= " faction-".$character->faction;
		}
		if(!$record instanceOf EpicDb_Interface_Cardable) return '';
		return $this->htmlTag("div", array('class' => 'inline-flow db-card transparent-bg rounded '.$params['class']), 
			$this->htmlTag("div", array('class' => 'record-icon inline-flow rounded'), 
				$this->cardScore($record)."".
				$this->link($record, array("text" => $this->htmlTag("img", array('src' => $this->getIcon($record)))))				
			)."".
			$this->htmlTag("div", array('class' => 'record-info inline-flow'), $this->cardDetails($record, $params))
		);
	}
}
