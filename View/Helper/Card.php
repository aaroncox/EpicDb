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
		if ($record->id) {
			if($record instanceOf EpicDb_Mongo_Profile) {
				return $this->view->profileLink($record, $params);
			}
			if($record->quality) $params += array('class' => 'quality-'.$record->quality);
			return $this->view->recordLink($record, $params);
		} else {
			return (isset($params['text'])) ? $params['text'] : $this->view->escape($record->name);
		}
	}
	public function addExtra($extra) {
		return $this->htmlTag("p", array("class" => 'text-small'), $extra);
	}
	protected function _detail($qualifier, $content) {
		if(!$qualifier || !$content) return '';
		if(is_int($qualifier)) return $this->htmlTag("p", array('class' => 'text-small'), $content);	
		return $this->htmlTag("p", array('class' => 'text-verysmall'), $qualifier)."".
			$this->htmlTag("p", array('class' => 'text-small'), $content);
	}
	public function getIcon($record) {
		return $record->getIcon();
	}
	public function setTagType($type) {
		$this->_tagType = $type;
		return $this;
	}
	protected $_tagType = "h4";
	public function cardDetails($record, $params = null) {
		$details = '';
		$details .= $this->htmlTag($this->_tagType, array('class' => 'text-medium'), $this->link($record, $params));
		if(!isset($params['content']) || $params['content'] === false) return $details;
		if(isset($params['content']) && !empty($params['content'])) {
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
		if(!$record instanceOf EpicDb_Mongo_Profile_User) return '';
		return $this->htmlTag("div", array("class" => "record-score"),
			$record->getLevel()
		);
	}
	public function unknownCard($params) {
		return $this->htmlTag("div", array('class' => 'inline-flow db-card rounded '.$params['class']),
			$this->htmlTag("div", array('class' => 'record-icon inline-flow'),
				$this->htmlTag("img", array('src' => 'http://s3.r2-db.com/unknown.jpg'))
			)."".
			$this->htmlTag("div", array('class' => 'record-info inline-flow'), $this->htmlTag("h2", array('class' => 'text-large'), 'Missing Account'))
		);
	}

	public function voteCard($record, $params) {
		if(!$record->acknowledged) {
			$extra = array("controls" => $this->view->button(array(
				'controller' => 'moderate',
				'action' => 'acknowledge',
				'vote' => $record->_id."",
			), 'default', true, array(
				'icon' => 'close',
				'text' => 'Acknowledge',
			)));
		} else {
			$extra = array(
				'acknowledged by ' => $this->view->profileLink($record->acknowledgedBy)." ".$this->view->timeAgo($record->acknowledged)
			);
		}
		$params['content'] = array("reported this ".$this->view->timeAgo($record->date) => $record->reason)+$extra;
		return $this->view->card($record->voter, $params);
		
	}
	
	public function cardDetailsWrapper($record, $params) {
		return $this->htmlTag("div", array('class' => 'record-info inline-flow'), $this->cardDetails($record, $params))."";
	}
	
	public function cardIconWrapper($record, $params) {
		return $this->htmlTag("div", array('class' => 'record-icon inline-flow '.$params['iconClass']), 
			$this->cardScore($record)."".
			$this->link($record, array("text" => $this->htmlTag("img", array('src' => $this->getIcon($record), 'alt' => $record->name, 'class' => 'icon')))+$params)
		)."";
	}

	public function card($record, $params = array()) {
		if($record instanceOf EpicDb_Vote_Abstract) return $this->voteCard($record, $params);
		// Reset for other times
		// var_dump($record);exit;
		$this->_tagType = 'h4';
		if(isset($params['tagType'])) $this->setTagType($params['tagType']);
		if(!$record) {
			$record = EpicDb_Mongo::newDoc('user');
			$record->name = "Anonymous";
			$record->email = "anonymous@r2-db.com";
		}
		if(!isset($params['extra'])) $params['extra'] = '';
		if(!isset($params['class'])) $params['class'] = '';
		if(!isset($params['iconClass'])) $params['iconClass'] = '';
		// Reimplement
		// if($record instanceOf EpicDb_Mongo_Profile_User && $record->characters && $character = $record->characters->getPrimary()) {
		// 	$params['class'] .= " faction-".$character->faction;
		// }
		if(!$record instanceOf EpicDb_Interface_Cardable) return '';
		if(isset($params['noContent']) && $params['noContent'] == true) {
			return $this->htmlTag("div", array('class' => 'inline-flow db-card rounded font-sans '.$params['class']), 
				$this->cardIconWrapper($record, $params)
			);
		}
		return $this->htmlTag("div", array('class' => 'inline-flow db-card rounded font-sans '.$params['class']), 
			$this->cardIconWrapper($record, $params)."".
			$this->cardDetailsWrapper($record, $params)
		);
	}
}
