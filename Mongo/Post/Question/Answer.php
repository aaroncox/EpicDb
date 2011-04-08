<?php
/**
 * EpicDb_Mongo_Post_Question_Answer
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_Post_Question_Answer extends EpicDb_Mongo_Post
{
	protected static $_collectionName = 'posts';
  protected static $_documentType = 'answer';
	protected static $_editForm = 'EpicDb_Form_Post_Question_Answer';


} // END class EpicDb_Mongo_Post_Question_Answer