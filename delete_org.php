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
 * Page for deleting organisation
 * @package    block_vxg_orgs
 * @copyright  Veloxnet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
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

$titlestring   = get_string('del_org', 'block_vxg_orgs');
$headingstring = get_string('del_org', 'block_vxg_orgs');

$PAGE->set_context($context);
$PAGE->set_url('/blocks/vxg_orgs/edit_org.php', array('orgid' => $orgid));
$PAGE->set_pagelayout('incourse');
$PAGE->set_title($titlestring);
$PAGE->navbar->add($headingstring);
$PAGE->set_heading($headingstring);

$org = $DB->get_record('block_vxg_orgs', array('id' => $orgid));

$orgchilds = $DB->count_records('block_vxg_orgs', ['parentid' => $org->id]);
$orgdata    = (object) array('org_childs' => $orgchilds, 'org_name' => $org->fullname);

$deleteorgsform = new \block_vxg_orgs\form\delete_orgs_form(null, array('orgdata' => $orgdata,
    'returnurl'                                                    => $returnurl));

$toform['orgid'] = $orgid;
$deleteorgsform->set_data($toform);

if ($deleteorgsform->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $deleteorgsform->get_data()) {
    delete_org_with_children($orgid);
    redirect($returnurl);
} else {
    $site = get_site();
    echo $OUTPUT->header();
    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
    $deleteorgsform->display();
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
}
