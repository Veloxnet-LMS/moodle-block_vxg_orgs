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

require_once '../../config.php';
require_once 'forms.php';

global $DB, $OUTPUT, $PAGE;

$orgid = optional_param('orgid', 0, PARAM_INT);
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);

require_login();
$context = context_system::instance();
require_capability('block/vxg_orgs:caneditorgs', $context);

if (empty($returnurl)) {
    $returnurl = new moodle_url('/my');
}

$title_string   =  get_string('add_org_title', 'block_vxg_orgs');
$heading_string =  get_string('add_org_heading', 'block_vxg_orgs');

$PAGE->set_context($context);
$PAGE->set_url('/blocks/vxg_orgs/add_org.php', array('orgid' => $orgid));
$PAGE->set_pagelayout('incourse');
$PAGE->set_title($title_string);
$PAGE->set_heading($heading_string);
$PAGE->navbar->add($heading_string);

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
$edit_orgs_form = new edit_orgs_form(null, array('toform' => $toform, 'data' => $data,
    'fileoptions' => $fileoptions,
));

if($edit_orgs_form->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $edit_orgs_form->get_data()) {

    if ($data->validfrom == 0) {
        $validfrom = NULL;
    }else{
        $validfrom = date('Y-m-d', $data->validfrom);
    }

    if ($data->validto == 0) {
        $validto = NULL;
    }else{
        $validto = date('Y-m-d', $data->validto);
    }

    if($orgid != 0){

        $parent_org = $DB->get_record('vxg_org', array('id' => $orgid), 'id, level, path');

        $org = new stdClass();
        $org->idnumber = $data->idnumber;
        $org->fullname = $data->fullname;
        $org->secondaryname = $data->secondaryname;
        $org->description = $data->description;
        $org->validfrom = $validfrom;
        $org->validto = $validto;
        $org->orgtype = $data->orgtype;
        $org->costcenterid = $data->costcenterid;

        $org->parentid = $parent_org->id;
        $org->level = $parent_org->level+1;
        $org->usermodified = $USER->id;
        $org->timecreated = date("Y/m/d/H/m/s");

        $inserted_id = $DB->insert_record('vxg_org', $org);

        $update_org = new stdClass();
        $update_org->id = $inserted_id;
        $update_org->path = $parent_org->path .'/' . $inserted_id;

        $DB->update_record('vxg_org', $update_org);
    } else {
        $org = new stdClass();
        $org->idnumber = $data->idnumber;
        $org->fullname = $data->fullname;
        $org->secondaryname = $data->secondaryname;
        $org->description = $data->description;
        $org->validfrom = $validfrom;
        $org->validto = $validto;
        $org->orgtype = $data->orgtype;
        $org->costcenterid = $data->costcenterid;
    
        $org->parentid = 0;
        $org->level = 1;
        $org->usermodified = $USER->id;
        $org->timecreated = date("Y/m/d/h/m/s");

        $inserted_id = $DB->insert_record('vxg_org', $org);

        $update_org = new stdClass();
        $update_org->id = $inserted_id;
        $update_org->path = '/' . $inserted_id;

        $DB->update_record('vxg_org', $update_org);
    }
    $filedata = file_postupdate_standard_filemanager($data, 'files',
            $fileoptions, $context, 'block_vxg_orgs_'. $inserted_id, 'files', 0);
    redirect($returnurl);
} else {
    $site = get_site();
    echo $OUTPUT->header();
    $edit_orgs_form->display();
    echo $OUTPUT->footer();
}