<?php
/*
 Extension Name: MiPagePlayer
 Extension Url: https://github.com/contructions-incongrues
 Description: Scraps playable links from current page and builds a global page player.
 Version: 0.1
 Author: Tristan Rivoallan <tristan@rivoallan.net>
 Author Url: http://github.com/trivoallan
 */

$Head->AddScript('extensions/MiPagePlayer/js/MiPagePlayer.playlist.js');
$Head->AddScript('extensions/MiPagePlayer/js/MiPagePlayer.behaviors.js?'.time());
$Head->AddStyleSheet('extensions/MiPagePlayer/css/MiPagePlayer.main.css?'.time());
$Context->AddToDelegate('CommentGrid', 'PostRender', 'MiPagePlayer_PostRenderCommentFoot');

function MiPagePlayer_PostRenderCommentFoot(CommentGrid $commentGrid) {
	$Context = $commentGrid->Context;
	include(dirname(__FILE__).'/templates/page-player.php');
}