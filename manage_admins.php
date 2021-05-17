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
 * Page for managing organisation admins
 * @package    block_vxg_orgs
 * @copyright  Veloxnet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/authlib.php');
require_once($CFG->dirroot . '/user/filters/lib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once(__DIR__ . '/locallib.php');
require_once(__DIR__ . '/vxg_user_filtering.php');

$orgid          = optional_param('orgid', 0, PARAM_INT);
$returnurl      = optional_param('returnurl', '', PARAM_LOCALURL);
$removeorgadmin = optional_param('removeorgadmin', 0, PARAM_INT);
$addorgadmin    = optional_param('addorgadmin', 0, PARAM_INT);

$sort    = optional_param('sort', 'name', PARAM_ALPHANUM);
$dir     = optional_param('dir', 'ASC', PARAM_ALPHA);
$page    = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 30, PARAM_INT); // Wow many per page.

require_login();
$context = context_system::instance();

require_capability('block/vxg_orgs:manageorgadmins', $context);
if (!$DB->record_exists('block_vxg_orgs', array('id' => $orgid))) {
    print_error('orgnotexist', 'block_vxg_orgs');
}

if (empty($returnurl)) {
    $returnurl = new moodle_url('/my');
}
$orgname = '';

$orgname = $DB->get_field('block_vxg_orgs', 'fullname', array('id' => $orgid));
$head    = get_string('manage_org_admins', 'block_vxg_orgs', $orgname);

$PAGE->set_context($context);
$PAGE->set_url('/blocks/vxg_orgs/manage_admins.php', array('orgid' => $orgid));
$PAGE->set_pagelayout('incourse');
$PAGE->set_title($head);
$PAGE->set_heading($head);
$PAGE->navbar->add($head);

if ($removeorgadmin && confirm_sesskey()) {
    require_capability('block/vxg_orgs:manageorgadmins', $context);
    if ($DB->record_exists('block_vxg_orgs_right',
    array('objecttype' => 'org', 'objectid' => $orgid, 'userid' => $removeorgadmin))) {
        $DB->delete_records('block_vxg_orgs_right',
        array('objecttype' => 'org', 'objectid' => $orgid, 'userid' => $removeorgadmin));
    }
} else if ($addorgadmin && confirm_sesskey()) {
    require_capability('block/vxg_orgs:manageorgadmins', $context);
    if (!$DB->record_exists('block_vxg_orgs_right', array('objecttype' => 'org', 'objectid' => $orgid, 'userid' => $addorgadmin))) {
        $DB->insert_record('block_vxg_orgs_right',
        (object) array('objecttype' => 'org', 'objectid' => $orgid, 'userid' => $addorgadmin));
    }
}

$fieldnames = array('realname' => 0, 'orgadmin'    => 0, 'org'        => 1, 'job' => 1, 'boss' => 1, 'lastname' => 1,
'firstname' => 1, 'username' => 1, 'email' => 1, 'idnumber' => 1, 'suspended' => 1, 'courserole' => 1,
    'systemrole'                   => 1, 'firstaccess' => 1, 'lastaccess' => 1,
    'timemodified'                 => 1);

// Create the user filter form.
$ufiltering = new vxg_user_filtering($fieldnames,
"manage_admins.php?orgid=$orgid&amp;sort=$sort&amp;dir=$dir&amp;returnurl=$returnurl", array('orgid' => $orgid));

echo $OUTPUT->header();

// These columns are always shown in the users list.
$requiredcolumns = array('idnumber');
// Extra columns containing the extra user fields, excluding the required columns (city and country, to be specific).
$extracolumns = get_extra_user_fields($context, $requiredcolumns);
// Get all user name fields as an array.
$allusernamefields = get_all_user_name_fields(false, null, null, null, true);
// Add vxg columns.
$vxgcolumns = array('job', 'org', 'boss');
$columns    = array_merge($allusernamefields, $extracolumns, $requiredcolumns);

foreach ($columns as $column) {
    $string[$column] = get_user_field_name($column);
    if ($sort != $column) {
        $columnicon = "";
        if ($column == "lastaccess") {
            $columndir = "DESC";
        } else {
            $columndir = "ASC";
        }
    } else {
        $columndir = $dir == "ASC" ? "DESC" : "ASC";
        if ($column == "lastaccess") {
            $columnicon = ($dir == "ASC") ? "sort_desc" : "sort_asc";
        } else {
            $columnicon = ($dir == "ASC") ? "sort_asc" : "sort_desc";
        }
        $columnicon = $OUTPUT->pix_icon('t/' . $columnicon, get_string(strtolower($columndir)), 'core',
            ['class' => 'iconsort']);
    }
    $$column = "<a href=\"manage_admins.php?orgid=$orgid&amp;sort=$column&amp;dir=$columndir&amp;returnurl=$returnurl\">" .
    $string[$column] . "</a>$columnicon";
}

foreach ($vxgcolumns as $column) {
    $$column = $string[$column] = get_string($column, 'block_vxg_orgs');
}

// We need to check that alternativefullnameformat is not set to '' or language.
// We don't need to check the fullnamedisplay setting here as the fullname function call further down has
// the override parameter set to true.
$fullnamesetting = $CFG->alternativefullnameformat;
// If we are using language or it is empty, then retrieve the default user names of just 'firstname' and 'lastname'.
if ($fullnamesetting == 'language' || empty($fullnamesetting)) {
    // Set $a variables to return 'firstname' and 'lastname'.
    $a            = new stdClass();
    $a->firstname = 'firstname';
    $a->lastname  = 'lastname';
    // Getting the fullname display will ensure that the order in the language file is maintained.
    $fullnamesetting = get_string('fullnamedisplay', null, $a);
}

// Order in string will ensure that the name columns are in the correct order.
$usernames       = order_in_string($allusernamefields, $fullnamesetting);
$fullnamedisplay = array();
foreach ($usernames as $name) {
    // Use the link from $$column for sorting on the user's name.
    $fullnamedisplay[] = ${$name};
}
// All of the names are in one column. Put them into a string and separate them with a /.
$fullnamedisplay = implode(' / ', $fullnamedisplay);
// If $sort = name then it is the default for the setting and we should use the first name to sort by.
if ($sort == "name") {
    // Use the first item in the array.
    $sort = reset($usernames);
}

list($extrasql, $params) = $ufiltering->get_sql_filter();
$users                   = get_users_listing($sort, $dir, $page * $perpage, $perpage, '', '', '',
    $extrasql, $params, $context);

$usercount       = get_users(false);
$usersearchcount = get_users(false, '', false, null, "", '', '', '', '', '*', $extrasql, $params);

if ($extrasql !== '') {
    echo $OUTPUT->heading("$usersearchcount / $usercount " . get_string('users'));
    $usercount = $usersearchcount;
} else {
    echo $OUTPUT->heading("$usercount " . get_string('users'));
}

$strall = get_string('all');

$baseurl = new moodle_url('/blocks/vxg_orgs/manage_admins.php',
array('orgid' => $orgid, 'sort' => $sort, 'dir' => $dir, 'perpage' => $perpage, 'returnurl' => $returnurl));
echo $OUTPUT->paging_bar($usercount, $page, $perpage, $baseurl);

flush();

if (!$users) {
    $match = array();
    echo $OUTPUT->heading(get_string('nousersfound'));

    $table = null;

} else {

    $table                      = new html_table();
    $table->head                = array();
    $table->colclasses          = array();
    $table->head[]              = $fullnamedisplay;
    $table->head[]              = $idnumber;
    $table->attributes['class'] = 'admintable generaltable';
    $table->head[]              = $boss;
    $table->head[]              = $job;
    $table->head[]              = $org;
    $table->head[]              = get_string('actions');
    $table->colclasses[]        = 'centeralign';
    $table->head[]              = "";
    $table->colclasses[]        = 'centeralign';

    $table->id = "vxg_orgadmins";
    foreach ($users as $user) {
        $buttons    = array();
        $lastcolumn = '';

        $positions = block_vxg_orgs_get_user_pos_list($user->id);
        $userjobs  = block_vxg_orgs_get_user_job_names($positions);
        $userorgs  = block_vxg_orgs_get_user_org_names($positions);
        $bosses    = block_vxg_orgs_get_user_boss_names($positions);

        $buttons = array();
        if ($DB->record_exists('block_vxg_orgs_right', array('objecttype' => 'org', 'objectid' => $orgid, 'userid' => $user->id))) {
            $removeorgadmin = new moodle_url('/blocks/vxg_orgs/manage_admins.php',
                array('orgid' => $orgid, 'removeorgadmin' => $user->id, 'returnurl' => $returnurl, 'sesskey' => sesskey()));
            $buttons[] = html_writer::link($removeorgadmin, $OUTPUT->pix_icon('t/removecontact',
            get_string('remove_org_admin', 'block_vxg_orgs'), 'moodle', array('class' => 'text-danger')));
        } else {
            $addorgadmin = new moodle_url('/blocks/vxg_orgs/manage_admins.php',
                array('orgid' => $orgid, 'addorgadmin' => $user->id, 'returnurl' => $returnurl, 'sesskey' => sesskey()));
            $buttons[] = html_writer::link($addorgadmin, $OUTPUT->pix_icon('i/assignroles',
             get_string('add_org_admin', 'block_vxg_orgs'), 'moodle', array('class' => 'text-primary')));
        }

        $fullname = fullname($user, true);
        // Get extra data for picture.
        $user->picture  = $DB->get_record('user', array('id' => $user->id), 'picture')->picture;
        $user->imagealt = $DB->get_record('user', array('id' => $user->id), 'imagealt')->imagealt;

        // Get the picture.
        $userpicture    = new \user_picture($user);
        $userpictureurl = $userpicture->get_url($PAGE)->out(false);

        // Make the link and a tag for a picture.
        $userprofileurl = new moodle_url('/user/profile.php', array('id' => $user->id));
        $profilepic     = html_writer::tag('img', '',
        array('src' => $userpictureurl, 'style' => 'margin-right:5px;border-radius:12px;'));

        $row            = array();
        $userprofileurl = new moodle_url('/user/profile.php', array('id' => $user->id));
        $row[]          = html_writer::link($userprofileurl, $profilepic . $fullname);
        $row[]          = $DB->get_record('user', array('id' => $user->id), 'idnumber')->idnumber;
        $row[]          = $bosses;
        $row[]          = $userjobs;
        $row[]          = $userorgs;
        if ($user->suspended) {
            foreach ($row as $k => $v) {
                $row[$k] = html_writer::tag('span', $v, array('class' => 'usersuspended'));
            }
        }
        $row[]         = implode(' ', $buttons);
        $row[]         = $lastcolumn;
        $table->data[] = $row;
    }
}
// Add filters.
$ufiltering->display_add();
$ufiltering->display_active();

if (!empty($table)) {
    echo html_writer::start_tag('div', array('class' => 'no-overflow'));
    echo html_writer::table($table);
    echo html_writer::end_tag('div');
    echo $OUTPUT->paging_bar($usercount, $page, $perpage, $baseurl);
}

echo $OUTPUT->footer();
