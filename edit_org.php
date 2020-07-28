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

global $DB, $OUTPUT, $PAGE, $USER;

$orgid     = required_param('orgid', PARAM_INT);
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);

require_login();
$context = context_system::instance();
require_capability('block/vxg_orgs:caneditorgs', $context);

if (empty($returnurl)) {
    $returnurl = new moodle_url('/my');
}

$title_string   = get_string('edit_org_title', 'block_vxg_orgs');
$heading_string = get_string('edit_org_heading', 'block_vxg_orgs');

$PAGE->set_context($context);
$PAGE->set_url('/blocks/vxg_orgs/edit_org.php', array('orgid' => $orgid));
$PAGE->set_pagelayout('incourse');
$PAGE->set_title($title_string);
$PAGE->set_heading($heading_string);
$PAGE->navbar->add($heading_string);

$org = $DB->get_record('vxg_org', array('id' => $orgid));

$fileoptions = array(
    'maxbytes'       => 0,
    'maxfiles'       => '-1',
    'subdirs'        => true,
    'accepted_types' => '*',
    'context'        => $context,
);

$data = new stdClass();
$data = file_prepare_standard_filemanager($data, 'files',
    $fileoptions, $context, 'block_vxg_orgs_' . $orgid, 'files', 0);

$toform['orgid']     = $orgid;
$toform['returnurl'] = $returnurl;

$edit_orgs_form = new edit_orgs_form(null, array('toform' => $toform, 'data' => $data,
    'fileoptions'                                             => $fileoptions,
));

if ($edit_orgs_form->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $edit_orgs_form->get_data()) {

    $filedata = file_postupdate_standard_filemanager($data, 'files',
        $fileoptions, $context, 'block_vxg_orgs_' . $orgid, 'files', 0);

    if ($data->validfrom == 0) {
        $validfrom = null;
    } else {
        $validfrom = date('Y-m-d', $data->validfrom);
    }

    if ($data->validto == 0) {
        $validto = null;
    } else {
        $validto = date('Y-m-d', $data->validto);
    }

    $org                = new stdClass();
    $org->id            = $orgid;
    $org->idnumber      = $data->idnumber;
    $org->fullname      = $data->fullname;
    $org->secondaryname = $data->secondaryname;
    $org->description   = $data->description;
    $org->validfrom     = $validfrom;
    $org->validto       = $validto;
    $org->orgtype       = $data->orgtype;
    $org->costcenterid  = $data->costcenterid;
    $org->timemodified  = date("Y/m/d/H/m/s");
    $org->usermodified  = $USER->id;
    $DB->update_record('vxg_org', $org);

    redirect($returnurl);
} else {

    $site = get_site();
    echo $OUTPUT->header();

    $edit_orgs_form->set_data(array(
        'idnumber'      => $org->idnumber,
        'fullname'      => $org->fullname,
        'secondaryname' => $org->secondaryname,
        'description'   => $org->description,
        'validfrom'     => strtotime($org->validfrom),
        'validto'       => strtotime($org->validto),
        'orgtype'       => $org->orgtype,
        'costcenterid'  => $org->costcenterid,

    ));

    $addurl = new moodle_url('/blocks/vxg_orgs/add_org.php', array('returnurl' => $returnurl,
        'orgid'                                                                    => $org->id));

    $add_btn = html_writer::link($addurl,
        html_writer::tag('button', get_string('add_org', 'block_vxg_orgs'),
            array('class' => 'btn btn-primary')));

    $delurl = new moodle_url('/blocks/vxg_orgs/delete_org.php', array('returnurl' => $returnurl,
        'orgid'                                                                       => $org->id));

    $del_btn = html_writer::link($delurl,
        html_writer::tag('button', get_string('del_org', 'block_vxg_orgs'),
            array('class' => 'btn btn-danger')));

    echo $add_btn;
    echo ' ';
    echo $del_btn;

    echo '<hr>';
    $edit_orgs_form->display();
    echo $OUTPUT->footer();

}
