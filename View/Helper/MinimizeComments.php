<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_View_Helper_MinimizeComments extends Zend_View_Helper_Abstract
{	
	public function minimizeComments($comments, $amount = 3) {
		$hidden = '';
		$shown = '';
		$total = count($comments);
		$skipTo = ($total - $amount) + 1;
		$skipped = $total - $skipTo;
		if($skipTo < 1) $skipTo = 1;
		$i = 1;
		foreach($comments as $comment) {
			if($i < $skipTo) {
				$hidden .= $this->view->partial("./post/_comment.phtml", array("comment" => $comment));
			} else {
				$shown .= $this->view->partial("./post/_comment.phtml", array("comment" => $comment));
			}
			$i++;
		}
		if($hidden) {
			$hidden = $this->view->htmlTag("div", array(), 
				$this->view->htmlTag("div", array("class" => "comments-show-button text-medium ui-state-default rounded"), "Show previous ".$skipped." comments")."".
				$this->view->htmlTag("div", array("class" => "comments-hidden"), $hidden)
			);			
		}
		return $this->view->htmlTag("div", array("class" => "comments-container"), 
			$hidden."".
			$shown."".
			$this->view->htmlTag("div", array("class" => "ui-helper-clearfix text-medium padded-10"), 
				$this->view->button(array(
						'action'=>'comment',
						'post'=>$comment->_parent,
					), 'post', true, array(
						'icon' => 'comment',
						'style' => 'float: right',
						'text' => 'Reply to these comments...',
						'data-tooltip' => 'Post a Reply to the Comments above.',
					)
				).""
			)
		);
	}
} // END class EpicDb_View_Helper_MinimizeComments extends Zend_View_Helper_Abstract

//	<div class="comments-container">
//		<?= $this->minimizeComments($comments)
//		foreach($comments as $comment):
//			$this->partial("./post/_comment.phtml", array("comment" => $comment))
//		endforeach;
//		<div class="ui-helper-clearfix text-medium padded-10">
			// $this->button(array(
			// 	'action'=>'comment',
			// 	'post'=>$this->post,
			// ), 'post', true, array(
			// 	'icon' => 'comment',
			// 	'style' => 'float: right',
			// 	'text' => 'Reply to these comments...',
			// 	'data-tooltip' => 'Post a Reply to the Comments above.',
			// ));
//		</div>
//	</div>
