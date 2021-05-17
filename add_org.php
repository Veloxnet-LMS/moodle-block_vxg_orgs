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
 * Page for adding new organisation
 * @package    block_vxg_orgs
 * @copyright  Veloxnet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/repository/lib.php');

global $DB, $OUTPUT, $PAGE;

$orgid = optional_param('orgid', 0, PARAM_INT);
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);

require_login();
$context = context_system::instance();
require_capability('block/vxg_orgs:caneditorgs', $context);

if (empty($returnurl)) {
    $returnurl = new moodle_url('/my');
}

$titlestring   = get_string('add_org_title', 'block_vxg_orgs');
$headingstring = get_string('add_org_heading', 'block_vxg_orgs');

$PAGE->set_context($context);
$PAGE->set_url('/blocks/vxg_orgs/add_org.php', array('orgid' => $orgid));
$PAGE->set_pagelayout('incourse');
$PAGE->set_title($titlestring);
$PAGE->set_heading($headingstring);
$PAGE->navbar->add($headingstring);

$fileoptions = array(
    'maxbytes' => 0,
    'maxfiles' => '-1',
    'subdirs' => true,
    'accepted_types' => '*',
    'context' => $context
);

$toform['orgid'] = $orgid;
$toform['returnurl'] = $returnurl;

$data = null;
$editorgsform = new \block_vxg_orgs\form\edit_orgs_form(null, array('toform' => $toform, 'data' => $data,
    'fileoptions' => $fileoptions,
));

if ($editorgsform->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $editorgsform->get_data()) {

    if ($orgid != 0) {

        $parentorg = $DB->get_record('block_vxg_orgs', array('id' => $orgid), 'id, level, path');

        $org = new stdClass();
        $org->idnumber = $data->idnumber;
        $org->fullname = $data->fullname;
        $org->secondaryname = $data->secondaryname;
        $org->description = $data->description;
        $org->validfrom = $data->validfrom;
        $org->validto = $data->validto;
        $org->orgtype = $data->orgtype;
        $org->costcenterid = $data->costcenterid;

        $org->parentid = $parentorg->id;
        $org->level = $parentorg->level + 1;
        $org->usermodified = $USER->id;
        $org->timecreated = time();

        $insertedid = $DB->insert_record('block_vxg_orgs', $org);

        $updateorg = new stdClass();
        $updateorg->id = $insertedid;
        $updateorg->path = $parentorg->path .'/' . $insertedid;

        $DB->update_record('block_vxg_orgs', $updateorg);
    } else {
        $org = new stdClass();
        $org->idnumber = $data->idnumber;
        $org->fullname = $data->fullname;
        $org->secondaryname = $data->secondaryname;
        $org->description = $data->description;
        $org->validfrom = $data->validfrom;
        $org->validto = $data->validto;
        $org->orgtype = $data->orgtype;
        $org->costcenterid = $data->costcenterid;

        $org->parentid = 0;
        $org->level = 1;
        $org->usermodified = $USER->id;
        $org->timecreated = time();

        $insertedid = $DB->insert_record('block_vxg_orgs', $org);

        $updateorg = new stdClass();
        $updateorg->id = $insertedid;
        $updateorg->path = '/' . $insertedid;

        $DB->update_record('block_vxg_orgs', $updateorg);
    }
    $filedata = file_postupdate_standard_filemanager($data, 'files',
            $fileoptions, $context, 'block_vxg_orgs_'. $insertedid, 'files', 0);
    redirect($returnurl);
} else {
    $site = get_site();
    echo $OUTPUT->header();
    $editorgsform->display();
    echo $OUTPUT->footer();
}
