<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file defines the setting form for the quiz grading report.
 *
 * @package   Report Mproctoring
 * @copyright Meetcs
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../../config.php');
require_once("$CFG->libdir/excellib.class.php");
require_login();
$downloadfilename = clean_filename("Report.xls");
$ve = 'quizaccess_mproctoring_ueve';
$u = 'user';
$quizid = $_REQUEST['quizid'];
$row = 1;
$firstinitial = isset($SESSION->gradereport['filterfirstname']) ? $SESSION->gradereport['filterfirstname'] : '';
$lastinitial = isset($SESSION->gradereport['filtersurname']) ? $SESSION->gradereport['filtersurname'] : '';
$workbook = new MoodleExcelWorkbook("-"); // Creating a workbook.
$formatbc = $workbook->add_format(); // Sending HTTP headers.
$formatbc->set_bold(1);
$formatbc->set_align('center');
$workbook->send($downloadfilename);
$strreport = "Report"; // Adding the worksheet.
$myxls = $workbook->add_worksheet($strreport);
$myxls->write_string(0 , 0 , 'First Name / Last Name', $formatbc);
$myxls->write_string(0, 1 , 'Email', $formatbc);
$myxls->write_string(0, 2 , 'Attempt', $formatbc);
$myxls->write_string(0, 3 , 'URL History', $formatbc);
$myxls->write_string(0, 4, 'Out Of Focus' , $formatbc);
$quizdata = $DB->get_record('quiz', array('id'  => $quizid)); // Print cellls.
$quizid = $quizdata->id;
if ($firstinitial) {
    $where = 'ue.quizid='.$quizid.' AND firstname LIKE "'.$firstinitial.'%"';
    $select = 'ue.id, u.id uid, u.firstname, u.picture, u.lastname, u.email,';
    $select = $select. ' ue.attempt, ue.eventsecond, ue.url as url1, ue.urlfilesize';
    $sql = 'SELECT '.$select.' FROM {'.$ve.'} as ue JOIN {'.$u.'} as u ON ue.userid=u.id where '.$where;
    $rec = $DB->get_records_sql($sql);
} else if ($lastinitial) {
    $where = 'ue.quizid=' . $quizid . ' AND lastname LIKE "' . $lastinitial . '%"';
    $select = 'ue.id, u.id uid, u.firstname, u.picture, u.lastname, u.email as email,';
    $select = $select. ' ue.attempt, ue.eventsecond, ue.url as url1, ue.urlfilesize';
    $sql = 'SELECT '.$select.' FROM {'.$ve.'} as ue Inner JOIN  {'.$u.'} as u ON ue.userid=u.id where '.$where;
    $rec = $DB->get_records_sql($sql);
} else if ($firstinitial && $lastinitial) {
    $where = ' ue.quizid=' . $quizid . ' AND firstname LIKE "' . $firstinitial . '%"AND lastname LIKE "' . $lastinitial . '%"';
    $select = 'ue.id, u.id uid, u.firstname, u.picture, u.lastname, u.email,';
    $select = $select . ' ue.attempt, ue.eventsecond, ue.url as url1, ue.urlfilesize';
    $sql = 'SELECT '.$select.' FROM {'.$ve.'} as ue Inner JOIN  {'.$u.'} as u ON ue.userid=u.id where '.$where;
    $rec = $DB->get_records_sql($sql);
} else {
    $where = ' ue.quizid=' . $quizid . ' AND firstname LIKE "' . $firstinitial . '%"';
    $select = 'ue.id, u.id as uid, u.picture, u.firstname, u.lastname, u.email,';
    $select = ' ue.attempt, ue.eventsecond, ue.url as url1, ue.urlfilesize';
    $sql = 'SELECT '.$select.' FROM {'.$ve.'} as ue Inner JOIN  {'.$u.'} as u ON ue.userid=u.id where '.$where;
    $rec = $DB->get_records_sql($sql);
}
foreach ($rec as $records) {
    $myxls->write_string($row, 0, trim($records->firstname." ".$records->lastname));
    $myxls->write_string($row, 1, trim($records->email));
    $myxls->write_string($row, 2, trim($records->attempt));
    $myxls->write_string($row, 3, trim($records->url1));
    $myxls->write_string($row, 4, trim (number_format((float)$records->eventsecond, 2, '.', '') . "%"));
    $row = $row + 1;
}  // Close the workbook.
$workbook->close();