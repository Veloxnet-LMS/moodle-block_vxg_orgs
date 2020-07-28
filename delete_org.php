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

require_once('../../config.php');
require_once('forms.php');
require_once('lib.php');

global $DB, $OUTPUT, $PAGE;

$orgid     = optional_param('orgid', 0, PARAM_INT);
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);

require_login();
$context = context_system::instance();
require_capability('block/vxg_orgs:caneditorgs', $context);

if (empty($returnurl)) {
    $returnurl = new moodle_url('/my');
}

$title_string   = get_string('del_org', 'block_vxg_orgs');
$heading_string = get_string('del_org', 'block_vxg_orgs');

$PAGE->set_context($context);
$PAGE->set_url('/blocks/vxg_orgs/edit_org.php', array('orgid' => $orgid));
$PAGE->set_pagelayout('incourse');
$PAGE->set_title($title_string);
$PAGE->navbar->add($heading_string);
$PAGE->set_heading($heading_string);

$org = $DB->get_record('vxg_org', array('id' => $orgid));

$org_childs = $DB->count_records('vxg_org', ['parentid' => $org->id]);
$orgdata    = (object) array('org_childs' => $org_childs, 'org_name' => $org->fullname);

$delete_orgs_form = new delete_orgs_form(null, array('orgdata' => $orgdata,
    'returnurl'                                                    => $returnurl));

$toform['orgid'] = $orgid;
$delete_orgs_form->set_data($toform);

if ($delete_orgs_form->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $delete_orgs_form->get_data()) {
    delete_org_with_children($orgid);
    redirect($returnurl);
} else {
    $site = get_site();
    echo $OUTPUT->header();
    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
    $delete_orgs_form->display();
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
}
