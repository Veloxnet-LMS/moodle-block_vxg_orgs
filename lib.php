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
 * The orgnaisation block helper functions
 * @package    block_vxg_orgs
 * @copyright  Veloxnet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/completionlib.php');

/**
 * Deletes the specified orgnasiantion and it's children
 * @param int   $orgid
 */
function block_vxg_orgs_delete_org_with_children($orgid) {
    global $DB;

    $childs = $DB->get_records('block_vxg_orgs', ['parentid' => $orgid], 'id');

    foreach ($childs as $child) {
        $child->deleted = time();
        block_vxg_orgs_delete_org_with_children($child->id);
        $DB->update_record('block_vxg_orgs', $child);

    }

    $DB->update_record('block_vxg_orgs', ['id' => $orgid, 'deleted' => time()]);
}

/**
 * File serving.
 *
 * @param stdClass $course The course object.
 * @param stdClass $cm The cm object.
 * @param context $context The context object.
 * @param string $filearea The file area.
 * @param array $args List of arguments.
 * @param bool $forcedownload Whether or not to force the download of the file.
 * @param array $options Array of options.
 * @return void|false
 */
function block_vxg_orgs_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {

    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'vxg_orgs', $filearea, $args[0], '/', $args[1]);

    send_stored_file($file);
}
