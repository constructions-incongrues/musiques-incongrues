<?php
/*
 Extension Name: MiSidebar
 Extension Url: https://github.com/contructions-incongrues
 Description: Handles http://www.musiques-incongrues.net sidebar
 Version: 0.1
 Author: Tristan Rivoallan <tristan@rivoallan.net>
 Author Url: http://github.com/trivoallan
 */

// TODO : move code to this extension
$Head->AddStyleSheet('extensions/SidepanelRotator/style.css');

// List all available blocks
// Sample structure
$blocks = array('sample' => array('html' => '', 'css' => array(''), 'js' => array()));

// Radio
$blocks['radio'] = array('html' => '
<h2>Écouter la radio</h2>
<a href="/forum/radio-random.php" onclick="window.open(this.href, \'Substantifique Mo&euml;lle Incongrue et Inodore\', \'height=700, width=340, top=100, left=100, toolbar=no, menubar=no, location=no, resizable=yes, scrollbars=no, status=no\'); return false;">
<br />
<img src="/forum/uploads/radio.png" alt="Écouter la radio" style="color:#666;text-align:center;" border="0px"/></a>
');

// Ailleurs
$blocks['ailleurs'] = array('html' => '
<h2>Ailleurs</h2>
<ul class="ailleurs-links">
	<li><a href="http://www.daheardit-records.net" title="Da ! Heard It Records">Da ! Heard It Records</a></li>
	<li><a href="http://www.egotwister.com" title="Ego Twister">Ego Twister</a></li>
	<li><a href="http://www.serendip-arts.org" title="Festival Serendip">Festival Serendip</a></li>
	<li><a href="http://istotassaca.blogspot.com/" title="Istota Ssaca">Istota Ssaca</a></li>
	<li><a href="http://lelaboratoire.be/" title="Le Laboratoire">Le Laboratoire</a></li>
	<li><a href="http://www.mazemod.org" title="Mazemod">Mazemod</a></li>
	<li><a href="http://www.musiqueapproximative.net" title="Musique Approximative">Musique Approximative</a></li>
	<li><a href="http://www.ouiedire.net" title="Ouïedire">Ouïedire</a></li>
	<li><a href="http://www.pardon-my-french.fr" title="Pardon My French">Pardon My French</a></li>
	<li><a href="http://www.thisisradioclash.org" title="Radioclash">Radioclash</a></li>
	<li><a href="http://thebrain.lautre.net" title="The Brain">The Brain</a></li>
	<li><a href="http://want.benetbene.net" title="WANT">WANT</a></li>
</ul>
');

// Affiner
$blocks['affiner'] = array('html' => '
<h2>Affiner</h2>
<ul>
	<li><a href="'.$Configuration['WEB_ROOT'].'discussions/?View=Bookmarks" >Discussions suivies</a></li> 
	<li><a href="'.$Configuration['WEB_ROOT'].'discussions/?View=YourDiscussions" >Discussions auquelles vous avez participé</a></li>
	<li><a href="'.$Configuration['WEB_ROOT'].'discussions/?View=Private" >Discussion privées</a></li>
	<li><a href="'.$Configuration['WEB_ROOT'].'search/?PostBackAction=Search&amp;Keywords=whisper;&amp;Type=Comments" >Commentaires chuchotés</a></li>
</ul>
');

// Introspection
// TODO : this should come from "Œil" extension
ob_implicit_flush(false);
ob_end_clean();
ob_start();
include(dirname(__FILE__).'/../SidePanelRotator/rotator.php');
$blocks['introspection'] = array('html' => ob_get_clean());

// Statistiques
// TODO : this is still provided by the "Statistics" extension

// Setup controller <=> blocks mappings
$mappings = array('default' => array('affiner', 'radio', 'introspection', 'ailleurs'));

// Select appropriate mapping
$mapping = $mappings['default'];
if (isset($mappings[$Context->SelfUrl])) {
	$mapping = $mappings[$Context->SelfUrl];
}

// Inject blocks into Panel
foreach ($mapping as $block) {
	if (isset($blocks[$block])) {
		$Panel->addString($blocks[$block]['html']);		
	}
}

//if (!($Context->SelfUrl == 'post.php' || $Context->SelfUrl == 'index.php' || $Context->SelfUrl == 'comments.php' || $Context->SelfUrl == 'extension.php' || $Context->SelfUrl == 'categories.php' || $Context->SelfUrl == 'search.php'))
//{
//  return;
//}
//
//
//// Limit access to thoses uids
//$uid = $Context->Session->UserID;
//if (!($uid == 1 || $uid == 2 || $uid == 47))
//{
//  return;
//}
