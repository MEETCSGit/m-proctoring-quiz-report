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
defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->dirroot . '/user/renderer.php');
require_once($CFG->dirroot . '/grade/lib.php');
require_once($CFG->dirroot . '/grade/report/grader/lib.php');
class quiz_mproctoring_report extends quiz_default_report {
    protected $cm;
    protected $quiz;
    protected $context;
    protected $course;
    public function display($quiz,  $cm,  $course) {
        global $CFG,  $DB,  $PAGE,  $COURSE,  $SESSION; // Get parameters.
        $groupids = optional_param_array('groupid', array(), PARAM_INT);
        $filter = optional_param('filter', 2, PARAM_INT);
        $sort = optional_param('sort', 1, PARAM_INT);
        $highlightcorrect = optional_param('highlightcorrect', 1, PARAM_INT);
        $print = optional_param('print', 0, PARAM_INT);
        $page = optional_param('page', 0, PARAM_INT); // active page.
        $courseid = required_param('id', PARAM_INT); // course id.
        $graderreportsifirst = optional_param('sifirst', null, PARAM_NOTAGS);
        $graderreportsilast = optional_param('silast', null, PARAM_NOTAGS);
        if (isset($graderreportsifirst)) {
            $SESSION->gradereport['filterfirstname'] = $graderreportsifirst;
        }
        if (isset($graderreportsilast)) {
            $SESSION->gradereport['filtersurname'] = $graderreportsilast;
        }
        $PAGE->set_url(new moodle_url('/grade/report/grader/index.php',  array('id' => $courseid)));
        if ($print == 1) { // Set appropriate page layout if necessary.
            $PAGE->set_pagelayout('base');
        }
        $context = context_module::instance($cm->id); // Get context.
        $reporturl = $CFG->wwwroot . '/mod/quiz/report.php';
        $this->print_header_and_tabs($cm,  $course,  $quiz,  'MProctoring'); // Start output.
        $quizdata = $DB->get_record('quiz',  array('id' => $cm->instance));
        $CFG->cachejs = false;
        if (is_siteadmin()) {
            $quizid = $quizdata->id;
            echo "</br>";
            $gpr = new grade_plugin_return( // u search.
            array('type' => 'report', 'plugin' => 'grader', 'course' => $course, 'page' => $page));
            $ue = 'mdl_quizaccess_mproctoring_ueve';
            $u = 'mdl_user';
            $context = context_course::instance($COURSE->id);
            $report = new grade_report_grader($COURSE->id,  $gpr,  $context,  $page,  $sort);
            $numusers = $report->get_numusers(true,  true);
            $url = new moodle_url($CFG->wwwroot . '/mod/quiz/report.php?id=4&mode=mproctoring');
            $firstinitial = isset($SESSION->gradereport['filterfirstname']) ? $SESSION->gradereport['filterfirstname'] : '';
            $lastinitial  = isset($SESSION->gradereport['filtersurname']) ? $SESSION->gradereport['filtersurname'] : '';
            $totalusers = $report->get_numusers(true,  false);
            $renderer = $PAGE->get_renderer('core_user');
            echo $renderer->user_search($url,  $firstinitial,  $lastinitial,  $numusers,  $totalusers);
            echo " <a class='btn btn-success' href=" . $CFG->wwwroot . "/mod/quiz/report/mproctoring/mproctoringreport.php?quizid="
            . $quizid . ">Download MProctoring Report </a>";
            echo "<br>";
            echo "<div id='abc'></div>";
            if (is_siteadmin()) {
                $quizid = $quizdata->id;
                if ($firstinitial) {
                    $where = 'ue.quizid='.$quizid.' AND firstname LIKE "'.$firstinitial.'%"';
                    $select = 'ue.id, u.id uid, u.firstname, u.picture, u.lastname, u.email, ue.attempt, ue.eventsecond, ue.url as url1, ue.urlfilesize';
                    $sql = 'SELECT '.$select.' FROM '.$ue.' as ue JOIN '.$u.' as u ON ue.userid=u.id where '.$where;
                    $rec = $DB->get_records_sql();
                } else if ($lastinitial) {
                    $where = 'ue.quizid=' . $quizid . ' AND lastname LIKE "' . $lastinitial . '%"';
                    $select = 'ue.id, u.id uid, u.firstname, u.picture, u.lastname, u.email as email, ue.attempt, ue.eventsecond, ue.url as url1, ue.urlfilesize';
                    $sql = 'SELECT '.$select.' FROM '.$ue.' as ue Inner JOIN  '.$u.' as u ON ue.userid=u.id where '.$where;
                    $rec = $DB->get_records_sql($sql);
                } else if ($firstinitial && $lastinitial) {
                    $where = ' ue.quizid=' . $quizid . ' AND firstname LIKE "' . $firstinitial . '%"AND lastname LIKE "' . $lastinitial . '%"';
                    $select = 'ue.id, u.id uid, u.firstname, u.picture, u.lastname, u.email, ue.attempt, ue.eventsecond, ue.url as url1, ue.urlfilesize';
                    $sql = 'SELECT '.$select.' FROM '.$ue.' as ue Inner JOIN  '.$u.' as u ON ue.userid=u.id where '.$where;
                    $rec = $DB->get_records_sql($sql);
                } else {
                    $where = ' ue.quizid=' . $quizid . ' AND firstname LIKE "' . $firstinitial . '%"';
                    $select = 'ue.id, u.id as uid, u.picture, u.firstname, u.lastname, u.email, ue.attempt, ue.eventsecond, ue.url as url1, ue.urlfilesize';
                    $sql = 'SELECT '.$select.' FROM '.$ue.' as ue Inner JOIN  '.$u.' as u ON ue.userid=u.id where '.$where;

                    $rec = $DB->get_records_sql($sql);
                }
                $table = new html_table();
                $table->head = array('Firstname / Lastname',  'Email',  "attempt",  "url",  "screencapture",  "eventsecond");
                foreach ($rec as $records) {
                    $firstname = $records->firstname;
                    $lastname = $records->lastname;
                    $email = $records->email;
                    $attempt = $records->attempt;
                    $urlfilesize = $records->urlfilesize;
                    $src = new moodle_url('/user/pix.php/' . $records->uid . '/f1.jpg');
                    $urlphoto = "<img src='" . $src . "'/>";
                    if ($records->picture) {
                        $src = new moodle_url('/user/pix.php/' . $records->uid . '/f2.jpg');
                        $urlphoto = "<img src='" . $src . "'/>";
                    }
                    if ($urlfilesize == '0') {
                        $url = "<b><a class=''  href='" . $CFG->wwwroot . "/mod/quiz/accessrule/mproctoring/download.php?url1=" . $records->url1 . "' >Download </a> </b>";
                    } else {
                        $url = "<b><a style='color:red' href='" . $CFG->wwwroot . "/mod/quiz/accessrule/mproctoring/download.php?url1=" . $records->url1 . "' >Download </a></b> ";
                    }
                    $eventsecond = number_format((float)$records->eventsecond,  2,  '.',  '') . "%";
                    $table->data[] = array($urlphoto,  $firstname . " " . $lastname,  $email,  $attempt,  $url,  $eventsecond);
                }
                $PAGE->requires->js_call_amd("quiz_mproctoring/quizattemptdata",  'init',  array($table->data));
            }
        }
    }
}