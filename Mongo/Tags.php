<?php
/**
 * EpicDb_Mongo_Tags
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_Tags extends Shanty_Mongo_DocumentSet
{
	protected $_requirements = array(
			'$' => array('Document:EpicDb_Mongo_Reference'),
			'$.reason' => array('Required'),
		);
		
	
	public function tag(MW_Mongo_Document $ref, $reason = 'tag') {
		$tag = $this->new();
		$tag->set($ref);
		$tag->reason = $reason;
		$this->addDocument($tag);
	}

	public function untag($ref = null, $reason = null) {
		if(is_string($ref)) {
			$reason = $ref;
			$ref = null;
		}
		if($ref && (!$ref instanceOf MW_Mongo_Document)) throw new Exception("Unknown Type");
		if($reason && !is_string($reason)) throw new Exception("Reason isn't a string");

		$query = array();
		if($ref) $query['ref'] = $ref->createReference();
		if($reason) $query['reason'] = $reason;
		foreach($this->export() as $idx => $tag) {
			if($query == $tag || (!$reason && $query['ref'] == $tag->ref) || (!$ref && $query['reason'] == $tag->reason)) {
				$this->setProperty($idx, null);
			}
		}
		return false;
	}
	public function hasTag($ref = null, $reason = null) {
		if(is_string($ref)) {
			$reason = $ref;
			$ref = null;
		}
		if($ref && (!$ref instanceOf MW_Mongo_Document)) throw new Exception("Unknown Type");
		if($reason && !is_string($reason)) throw new Exception("Reason isn't a string");

		$query = array();
		if($ref) $query['ref'] = $ref->createReference();
		if($reason) $query['reason'] = $reason;
		foreach($this->export() as $tag) {
			if($query == $tag) return true;
			if(!$reason && $query['ref'] == $tag->ref) return true;
			if(!$ref && $query['reason'] == $tag->reason) return true;
		}
		return false;
	}
	
	public function getTag($reason) {
		foreach($this as $tag) {
			if($tag->reason == $reason) return $tag->ref;
		}		
		return null;
	}
	
	public function getTags($reason)
	{
		$return = array();
		foreach($this as $tag) {
			if ($tag->reason == $reason) $return[] = $tag->ref;
		}
		return $return;
	}

	public function setTags($tags, $reason)
	{
		$refs = array();
		foreach ($tags as $idx => $tag) { 
			$refs[$idx] = $tag->createReference();
		}
		$now = array();
		foreach($this as $idx => $tag) {
			if ($tag->reason == $reason) {
				$now[$idx] = $tag->ref->createReference();
				$test = in_array($now[$idx], $refs);
				if (!$test) $this->setProperty($idx, null);
			}
		}
		foreach ($refs as $idx => $ref) {
			if (!in_array($ref, $now)) {
				$tag = $this->new();
				$tag->ref = $tags[$idx];
				$tag->reason = $reason;
				$this->addDocument($tag);
			}
		}
		return $this;
	}
	
	public function setTag($reason, $tag) {
		$this->setTags(array($tag), $reason);
		return $this;
	}
} // END class EpicDb_Mongo_Tags