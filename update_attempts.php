<?php

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');


$quizid = required_param('quiz', PARAM_INT);
$cmid = required_param('cmid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);

if (!$cm = get_coursemodule_from_id('quiz', $cmid)) {
      error("Course module ID was incorrect");
}

$context = context_module::instance($cm->id);


if(!has_capability('mod/quiz:manage', $context)) {
  add_to_log($cmid, 'quiz', 'updateattempts', 'mod/quiz/update_attempts.php?quiz='.$quizid.'&id='.$cmid, 'Not allowd user attempting to update quiz');
  redirect(new moodle_url('/mod/quiz/view.php', array('id'=>$cmid)));
}
 

global $CFG;
global $DB;

$res = $DB->get_records('quiz_attempts',array('quiz'=>$quizid));

foreach($res as $resid=>$result)
{
  if($result->timefinish == 0) {
    $quiz = $DB->get_records('quiz', array('id'=>$result->quiz));
    $update = new stdClass();
    $update->id = $resid;
//    $update->timefinish = $result->timestart + 5 * 60;
    $update->timefinish = $quiz->timeclose;
    $DB->update_record('quiz_attempts',$update);
  }
}

add_to_log($cmid, 'quiz', 'updateattempts', 'mod/quiz/update_attempts.php?quiz='.$quizid.'&id='.$cmid, 'Updated attempts for quizID:'.$quizid);

/*
$tmp = $DB->update_records_sql("UPDATE quiz_attempts set timefinish = timestart + 5 * 60 where timefinish = 0 AND quiz = ".$quizid);
add_to_log($cmid,"UPDATE ".$CFG->prefix."quiz_attempts set timefinish = timestart + 5 * 60 where
timefinish = 0 AND quiz = ".$quizid.";");
*/
//redirect(new moodle_url('/course/view.php', array('id'=>$cmid)));
redirect(new moodle_url('/mod/quiz/view.php', array('id'=>$cmid)));
