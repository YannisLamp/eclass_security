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

/*===========================================================================
	grade_edit.php
 * @version $Id: grade_edit.php,v 1.13 2008-07-28 14:15:27 antonism Exp $
	@author: Dionysios G. Synodinos <synodinos@gmail.com>
	@author: Evelthon Prodromou <eprodromou@upnet.gr>
==============================================================================        
*/

$require_current_course = TRUE;

include '../../include/baseTheme.php';
$tool_content = "";
include('work_functions.php');

$nameTools = $m['grades'];
mysql_select_db($currentCourseID);

if ($is_adminOfCourse and isset($_GET['assignment']) and isset($_GET['submission'])) {
		$assign = get_assignment_details($_GET['assignment']);
		$navigation[] = array("url"=>"work.php", "name"=>$langWorks);
		$navigation[] = array("url"=>"work.php?id=$_GET[assignment]", "name"=>$m['WorkView']);
		show_edit_form($_GET['assignment'], $_GET['submission'], $assign);
		draw($tool_content, 2);
} else {
		header('Location: work.php');
		exit;
}

// Returns an array of the details of assignment $id
function get_assignment_details($id)
{
	return mysql_fetch_array(db_query("SELECT * FROM assignments WHERE id = '".mysql_real_escape_string($id)."'"));
}


// Show to professor details of a student's submission and allow editing of fields
// $assign contains an array with the assignment's details
function show_edit_form($id, $sid, $assign)
{
	global $m, $langGradeOk, $tool_content, $langGradeWork;

	if ($sub = mysql_fetch_array(db_query("SELECT * FROM assignment_submit WHERE id = '".mysql_real_escape_string($sid)."'"))) {
		
		$uid_2_name = uid_to_name($sub['uid']);
		if (!empty($sub['group_id'])) {
					$group_submission = "($m[groupsubmit] ".
						"<a href='../group/group_space.php?userGroupId=".htmlspecialchars($sub[group_id])."'>".
						htmlspecialchars($m[ofgroup]). htmlspecialchars($sub[group_id])."</a>)";
			} else $group_submission = "";
    //<<<cData
      $tool_content .= ' 
    <form method="post" action="work.php">
    <input type="hidden" name="assignment" value="'.htmlspecialchars($id).'">
    <input type="hidden" name="submission" value="'.htmlspecialchars($sid).'">

    <table width="99%" class="FormData">
    <tbody>
    <tr>
      <th width="220">&nbsp;</th>
       <td><b>'.htmlspecialchars($m[addgradecomments]).'</b></td>
    </tr>
    <tr>
      <th class="left">'.htmlspecialchars($m['username']).':</th>
      <td>'.htmlspecialchars($uid_2_name). htmlspecialchars($group_submission).'</td></tr>
    <tr>
      <th class="left">'.htmlspecialchars($m['sub_date']).':</th>
      <td>'.htmlspecialchars($sub['submission_date']).'</td></tr>
    <tr>
      <th class="left">'.htmlspecialchars($m['filename']).':</th>
      <td><a href=\'work.php?get='.htmlspecialchars($sub['id']).'\'>'.htmlspecialchars($sub['file_name']).'</a></td>
    </tr>';
//cData;

  $tool_content .=  '
    <tr>
      <th class="left">'.htmlspecialchars($m['grade']).':</th>
      <td><input type="text" name="grade" maxlength="3" size="3" value="'.htmlspecialchars($sub['grade']).'" class="FormData_InputText"></td>
    </tr>
    <tr>
      <th class="left">'.htmlspecialchars($m['gradecomments']).':</th>
      <td><textarea cols="60" rows="3" name="comments" class="FormData_InputText">'.htmlspecialchars($sub['grade_comments']).'</textarea></td>
    </tr>
    <tr>
      <th class="left">&nbsp;</th>
      <td><input type="submit" name="grade_comments" value="'.htmlspecialchars($langGradeOk).'"></td>
    </tr>
    </tbody>
    </table>

    </form>
    <br/>';
//cData;

	} else {
		$tool_content .= "<p>error - no such submission with id ".htmlspecialchars($sid)."</p>\n";
	}
}

?>
