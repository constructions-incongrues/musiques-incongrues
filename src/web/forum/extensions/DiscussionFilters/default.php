<?php
/*
 Extension Name: Discussion Filters
 Extension Url: http://lussumo.com/docs/
 Description: Adds links to the control panel which allow users to filter the discussion list (or search results) to user-centric data like "Bookmarked Discussions", "Your Discussions", "Whispered Discussions", and "Whispered Comments".
 Version: 2.0
 Author: Mark O'Sullivan
 Author Url: http://markosullivan.ca/

 Copyright 2003 - 2005 Mark O'Sullivan
 This file is part of Vanilla.
 Vanilla is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 Vanilla is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 You should have received a copy of the GNU General Public License along with Vanilla; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 The latest source code for Vanilla is available at www.lussumo.com
 Contact Mark O'Sullivan at mark [at] lussumo [dot] com

 You should cut & paste these language definitions into your
 conf/your_language.php file (replace "your_language" with your chosen language,
 of course):
 */

// NOTE : this is now handled by the MiSidebar extension
//if (in_array($Context->SelfUrl, array("categories.php", "comments.php", "index.php", "post.php")) && $Context->Session->UserID > 0) {
//
//   $DiscussionFilters = $Context->GetDefinition("DiscussionFilters");
//   $Panel->AddList($DiscussionFilters, 0);
//   if (!$Context->Session->User->Preference("ShowBookmarks")) $Panel->AddListItem($DiscussionFilters, $Context->GetDefinition("BookmarkedDiscussions"), GetUrl($Configuration, "index.php", "", "", "", "", "View=Bookmarks"), "", "", 10);
//   if (!$Context->Session->User->Preference("ShowRecentDiscussions")) $Panel->AddListItem($DiscussionFilters, $Context->GetDefinition("YourDiscussions"), GetUrl($Configuration, "index.php", "", "", "", "", "View=YourDiscussions"), "", "", 20);
//   if ($Configuration["ENABLE_WHISPERS"] && !$Context->Session->User->Preference("ShowPrivateDiscussions")) {
//      $Panel->AddListItem($DiscussionFilters, $Context->GetDefinition("PrivateDiscussions"), GetUrl($Configuration, "index.php", "", "", "", "", "View=Private"), "", "", 30);
//      $Panel->AddListItem($DiscussionFilters, $Context->GetDefinition("PrivateComments"), GetUrl($Configuration, "search.php", "", "", "", "", "PostBackAction=Search&amp;Keywords=whisper;&amp;Type=Comments"), "", "", 40);
//   }
//}

// Apply any necessary filters
if ($Context->SelfUrl == "index.php") {
	$View = ForceIncomingString("View", "");
	switch ($View) {
		case "Bookmarks":
			$Context->PageTitle = $Context->GetDefinition("BookmarkedDiscussions");
			function DiscussionManager_FilterDataToBookmarks(&$DiscussionManager) {
				$s = &$DiscussionManager->DelegateParameters['SqlBuilder'];
				$s->AddWhere('b', 'DiscussionID', 't', 'DiscussionID', '=', 'and', '', 0, 1);
				$s->AddWhere('b', 'UserID', '', $DiscussionManager->Context->Session->UserID, '=');
				$s->EndWhereGroup();
			}
			$Context->AddToDelegate("DiscussionManager",
            "PreGetDiscussionList",
            "DiscussionManager_FilterDataToBookmarks");
			$Context->AddToDelegate("DiscussionManager",
            "PreGetDiscussionCount",
            "DiscussionManager_FilterDataToBookmarks");
			break;

		case "YourDiscussions":
			$Context->PageTitle = $Context->GetDefinition("YourDiscussions");
			function DiscussionManager_FilterDataToOwnDiscussions(&$DiscussionManager) {
				$s = &$DiscussionManager->DelegateParameters['SqlBuilder'];
				$s->AddWhere('t', 'AuthUserID', '', $DiscussionManager->Context->Session->UserID, '=');
			}
			$Context->AddToDelegate("DiscussionManager",
            "PreGetDiscussionList",
            "DiscussionManager_FilterDataToOwnDiscussions");
			$Context->AddToDelegate("DiscussionManager",
            "PreGetDiscussionCount",
            "DiscussionManager_FilterDataToOwnDiscussions");
			break;

		case "Private":
			$Context->PageTitle = $Context->GetDefinition("PrivateDiscussions");
			function DiscussionManager_FilterDataToPrivateDiscussions(&$DiscussionManager) {
				$s = &$DiscussionManager->DelegateParameters['SqlBuilder'];
				$s->AddWhere('t', 'WhisperUserID', '', $DiscussionManager->Context->Session->UserID, '=', 'and', '', 0, 1);
				$s->AddWhere('t', 'AuthUserID', '', $DiscussionManager->Context->Session->UserID, '=', 'or', '', 0, 1);
				$s->AddWhere('t', 'WhisperUserID', '', 0, '>', 'and');
				$s->EndWhereGroup();
				$s->EndWhereGroup();
			}
			$Context->AddToDelegate("DiscussionManager",
            "PreGetDiscussionList",
            "DiscussionManager_FilterDataToPrivateDiscussions");
			$Context->AddToDelegate("DiscussionManager",
            "PreGetDiscussionCount",
            "DiscussionManager_FilterDataToPrivateDiscussions");
			break;
			 
		case "ByUser":
			$userName = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_STRING);
			$Context->PageTitle = sprintf('Discussion(s) initiée(s) par %s', $userName);
			function DiscussionManager_FilterDataByUserName($DiscussionManager) {
				$userName = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_STRING);
				$s = $DiscussionManager->DelegateParameters['SqlBuilder'];
				$s->AddWhere('u', 'Name', '', $userName, '=');
			}
			$Context->AddToDelegate("DiscussionManager",
            "PreGetDiscussionList",
            "DiscussionManager_FilterDataByUserName");
			// TODO : counting generates an sql error
//			$Context->AddToDelegate("DiscussionManager",
//            "PreGetDiscussionCount",
//            "DiscussionManager_FilterDataByUserName");
			break;
			 
	}
}