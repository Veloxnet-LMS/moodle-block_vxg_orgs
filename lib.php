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

defined('MOODLE_INTERNAL') || die();

require_once $CFG->libdir . '/completionlib.php';

function delete_org_with_children($orgid){
    global $DB;

    $childs = $DB->get_records('vxg_org', ['parentid' => $orgid], 'id');

    foreach ($childs as $child) {
        $child->deleted = date("Y/m/d/H/m/s");
        delete_org_with_children($child->id);
        $DB->update_record('vxg_org', $child);
        
    }

    $DB->update_record('vxg_org', ['id' => $orgid, 'deleted' => date("Y/m/d/H/m/s")]);
}


function block_vxg_orgs_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {

    // if ($context->contextlevel != CONTEXT_SYSTEM) {
    //     send_file_not_found();
    // }

    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'vxg_orgs', $filearea, $args[0], '/', $args[1]);

    send_stored_file($file);
}