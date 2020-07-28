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


function block_vxg_orgs_get_assignable_roles()
{
    global $DB;

    $role_ids = $DB->get_fieldset_select('role_context_levels', 'roleid',
        'contextlevel = ? OR contextlevel = ? OR contextlevel = ?', array('10', '40', '50'));

    $insql = 'IN (' . implode(',', $role_ids) . ')';

    $sql = 'SELECT shortname FROM {role} WHERE id ' . $insql . ' ORDER BY id';

    $role_names = $DB->get_fieldset_sql($sql);

    $role_names = array_combine(array_values($role_names), array_values($role_names));

    return $role_names;

}

function block_vxg_orgs_get_user_role_names()
{
    global $USER, $COURSE;

    $user_roles = get_user_roles(context_course::instance($COURSE->id), $USER->id);

    $role_names = array();
    foreach ($user_roles as $role) {
        $role_names[] = $role->shortname;
    }

    return $role_names;

}

// User filtering functions

function block_vxg_orgs_get_user_pos_list($userid)
{
    global $DB;

    if(!$DB->get_manager()->table_exists('vxg_user_pos')){
        return array();
    }
    $sql       = 'SELECT up.`id`, p.`orgid`, p.`jobid`, p.`parentid`, up.`validfrom`, up.`validto` FROM {vxg_user_pos} up LEFT JOIN {vxg_pos} p ON up.`posid` = p.`id` WHERE up.`userid` = :userid';
    $positions = $DB->get_records_sql($sql, array('userid' => $userid));

    return $positions;
}
function block_vxg_orgs_get_user_job_names($positions)
{
    global $DB;

    foreach ($positions as $position) {
        $jobs = $DB->get_record('vxg_job', array('id' => $position->jobid), 'id, fullname');
        if (isset($jobs) && !empty($jobs)) {
            $jobnames[] = $jobs->fullname;
        }
    }
    return isset($jobnames) && !empty($jobnames) ? implode(', ', $jobnames) : '';
}

function block_vxg_orgs_get_user_org_names($positions)
{
    global $DB;

    foreach ($positions as $position) {
        $orgs = $DB->get_record('vxg_org', array('id' => $position->orgid), 'id, fullname');
        if (isset($orgs) && !empty($orgs)) {
            $orgnames[] = $orgs->fullname;
        }
    }
    return isset($orgnames) && !empty($orgnames) ? implode(', ', $orgnames) : '';
}
function block_vxg_orgs_get_user_boss_names($positionsparents)
{
    global $DB;

    foreach ($positionsparents as $parent) {
        $boss = $DB->get_record('vxg_pos', array('id' => $parent->parentid), 'id, fullname');
        if (isset($boss) && !empty($boss)) {
            $bossnames[] = $boss->fullname;
        }
    }
    return isset($bossnames) && !empty($bossnames) ? implode(', ', $bossnames) : '';
}