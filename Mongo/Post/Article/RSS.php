<?php
/**
 * EpicDb_Mongo_Record_Skill
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 **/
class EpicDb_Mongo_Post_Article_RSS extends EpicDb_Mongo_Post_Article implements EpicDb_Vote_Interface_UpOnly
{
  protected static $_documentType = 'article-rss';
	
	public static function getTopArticle($query = array(), $sort = array()) {
		$sort['votes.score'] = -1;
		$results = self::fetchAll($query, $sort, 3);
		foreach($results as $result) {
			return $result;
		}
		return null;
	}
	
	public static function retrieveArticle($website, $article) {
		// var_dump($article->getId()); exit;
		if(is_object($article)) {
			$siteId = $article->getId();			
		} else {
			$siteId = $article['contentUrl'];
		}
		$query = array(
			'siteId' => $siteId,
			'tags' => array(
				'$elemMatch' => array(
					'reason' => 'source',
					'ref' => $website->createReference(),
				)
			)
		);
		$row = static::fetchOne($query);
		if(!$row) {
			$row = new self();
			$row->siteId = $siteId;
			$row->tags->tag($website, 'source');
		}
		return $row;
	}
}