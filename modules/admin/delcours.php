<?php
/*========================================================================
*   Open eClass 2.3
*   E-learning and Course Management System
* ========================================================================
*  Copyright(c) 2003-2010  Greek Universities Network - GUnet
*  A full copyright notice can be read in "/info/copyright.txt".
*
*  Developers Group:	Costas Tsibanis <k.tsibanis@noc.uoa.gr>
*			Yannis Exidaridis <jexi@noc.uoa.gr>
*			Alexandros Diamantidis <adia@noc.uoa.gr>
*			Tilemachos Raptis <traptis@noc.uoa.gr>
*
*  For a full list of contributors, see "credits.txt".
*
*  Open eClass is an open platform distributed in the hope that it will
*  be useful (without any warranty), under the terms of the GNU (General
*  Public License) as published by the Free Software Foundation.
*  The full license can be read in "/info/license/license_gpl.txt".
*
*  Contact address: 	GUnet Asynchronous eLearning Group,
*  			Network Operations Center, University of Athens,
*  			Panepistimiopolis Ilissia, 15784, Athens, Greece
*  			eMail: info@openeclass.org
* =========================================================================*/

/**===========================================================================
	delcours.php
	@last update: 31-05-2006 by Pitsiougas Vagelis
	@authors list: Karatzidis Stratos <kstratos@uom.gr>
		       Pitsiougas Vagelis <vagpits@uom.gr>
==============================================================================
        @Description: Delete a course

 	This script allows the administrator to delete a course

 	The user can : - Confirm for course deletion
 	               - Delete a cours
                 - Return to course list

 	@Comments: The script is organised in three sections.

  1) Confirm course deletion
  2) Delete course
  3) Display all on an HTML page

==============================================================================*/

/*****************************************************************************
		DEAL WITH BASETHEME, OTHER INCLUDES AND NAMETOOLS
******************************************************************************/
// Check if user is administrator and if yes continue
// Othewise exit with appropriate message
$require_admin = TRUE;
// Include baseTheme
include '../../include/baseTheme.php';
if(!isset($_POST['c'])) { die(); }
//if(empty($_SESSION['token'])){ die();}
// Define $nameTools
$nameTools = $langCourseDel;
$navigation[] = array("url" => "index.php", "name" => $langAdmin);
$navigation[] = array("url" => "listcours.php", "name" => $langListCours);
// Initialise $tool_content
$tool_content = "";
/*****************************************************************************
		MAIN BODY
******************************************************************************/
// Initialize some variables
$searchurl = "";

// Define $searchurl to go back to search results
if (isset($search) && ($search=="yes")) {
	$searchurl = "&search=yes";
}
// Delete course
if (isset($_POST['delete']) && isset($_POST['c']) && !empty($_POST['token']) && (strcmp($_SESSION['token'], $_POST['token']) === 0))  {
	db_query("DROP DATABASE `".mysql_real_escape_string($_POST['c'])."`");
        mysql_select_db($mysqlMainDb);
        $code = quote($_POST['c']);
	db_query("DELETE FROM cours_faculte WHERE code = $code");
	db_query("DELETE FROM cours_user WHERE cours_id =
                        (SELECT cours_id FROM cours WHERE code = $code)");
	db_query("DELETE FROM annonces WHERE cours_id =
                        (SELECT cours_id FROM cours WHERE code = $code)");
	db_query("DELETE FROM cours WHERE code = $code");
	@mkdir("../../courses/garbage");
	rename("../../courses/".$_POST['c'], "../../courses/garbage/".$_POST['c']);
	$tool_content .= "<p>".$langCourseDelSuccess."</p>";
}
// Display confirmatiom message for course deletion
else {
	$row = mysql_fetch_array(mysql_query("SELECT * FROM cours WHERE code='".mysql_real_escape_string($_POST['c'])."'"));

	$tool_content .= "<table><caption>".$langCourseDelConfirm."</caption><tbody>";
	$tool_content .= "  <tr>
    <td><br />".$langCourseDelConfirm2." <em>".htmlspecialchars($_POST['c'])."</em>;<br /><br /><i>".$langNoticeDel."</i><br /><br /></td>
  </tr>";
	$tool_content .= "  <tr>
    <td><ul><li>
		<form id='myform".htmlspecialchars($_POST['c'])."' action='delcours.php' method='post'>
			<a href='javascript:;' onclick=\"document.getElementById('myform".htmlspecialchars($_POST['c'])."').submit();\">
			<b>$langYes</b></a>
			<input type='hidden' name='c' value='".htmlspecialchars($_POST['c'])."'/>
			<input type='hidden' name='delete' value='yes' />
			<input type='hidden' name='token' value='$token'/>
		</form>
		<br />&nbsp;</li>
  <li><a href=\"listcours.php?c=".htmlspecialchars($_POST['c'])."".$searchurl."\"><b>$langNo</b></a></li></ul></td>
  </tr>";
	$tool_content .= "</tbody></table><br />";
}
// If course deleted go back to listcours.php
if (isset($_POST['c']) && !isset($delete)) {
	$tool_content .= "<center><p><a href=\"listcours.php?c=".htmlspecialchars($_POST['c'])."".$searchurl."\">".$langBack."</a></p></center>";
}
// Go back to listcours.php
else {
	// Display link to listcours.php with search results
	if (isset($search) && ($search=="yes")) {
		$tool_content .= "<center><p><a href=\"listcours.php?search=yes\">".$langReturnToSearch."</a></p></center>";
	}
	// Display link to listcours.php
	$tool_content .= "<center><p><a href=\"listcours.php\">$langBack</a></p></center>";
}

/*****************************************************************************
		DISPLAY HTML
******************************************************************************/
// Call draw function to display the HTML
// $tool_content: the content to display
// 3: display administrator menu
// admin: use tool.css from admin folder
draw($tool_content,3, 'admin');
