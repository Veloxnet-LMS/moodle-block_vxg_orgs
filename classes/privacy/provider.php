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
 * Privacy provider for block_vxg_orgs.
 *
 * @package    block_vxg_orgs
 * @category   privacy
 * @copyright  Veloxnet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_vxg_orgs\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\approved_userlist;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\transform;
use \core_privacy\local\request\userlist;
use \core_privacy\local\request\writer;

/**
 * Privacy provider for block_vxg_orgs.
 *
 * @package    block_vxg_orgs
 * @copyright  Veloxnet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
\core_privacy\local\metadata\provider,
\core_privacy\local\request\subsystem\provider,
\core_privacy\local\request\core_userlist_provider
{

    /**
     * Get information about the user data stored by this plugin.
     *
     * @param  collection $collection An object for storing metadata.
     * @return collection The metadata.
     */
    public static function get_metadata(collection $collection): collection {
        $orgs = [
            'timecreated'  => 'privacy:metadata:block_vxg_orgs:timecreated',
            'timemodified' => 'privacy:metadata:block_vxg_orgs:timemodified',
            'usermodified' => 'privacy:metadata:block_vxg_orgs:usermodified',
        ];
        $orgsrights = [
            'roleid'       => 'privacy:metadata:block_vxg_orgs_right:roleid',
            'userid'       => 'privacy:metadata:block_vxg_orgs_right:userid',
            'timemodified' => 'privacy:metadata:block_vxg_orgs_right:timemodified',
            'usermodified' => 'privacy:metadata:block_vxg_orgs_right:usermodified',
        ];
        $collection->add_database_table('block_vxg_orgs', $orgs,
            'privacy:metadata:block_vxg_orgs:tableexplanation');
        $collection->add_database_table('block_vxg_orgs_right', $orgsrights,
            'privacy:metadata:block_vxg_orgs_right:tableexplanation');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        $contextlist->add_user_context($userid);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if (!$context instanceof \context_user) {
            return;
        }

        // No data available for this user.
        if (!$DB->record_exists('block_vxg_orgs_right', ['userid' => $context->instanceid])) {
            return;
        }

        $userlist->add_user($context->instanceid);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $user = $contextlist->get_user();

        $context = \context_user::instance($user->id);

        $pluginname = get_string('pluginname', 'block_vxg_orgs');
        $orgadmins = get_string('org_admins', 'block_vxg_orgs');

        $params = [
            'userid' => $user->id,
        ];

        $records = $DB->get_recordset('block_vxg_orgs_right', $params);

        foreach ($records as $record) {

            $item = (object) [
                'objecttype'   => $record->objecttype,
                'roleid'       => $record->roleid,
                'userid'       => $record->userid,
                'righttype'    => $record->righttype,
                'timemodified' => transform::datetime($record->timemodified),
            ];

            writer::with_context($context)->export_data([$pluginname, $orgadmins], $item);
        }
        $records->close();

    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context->contextlevel != CONTEXT_USER) {
            return;
        }
        $DB->delete_records('block_vxg_orgs_right', $context->instanceid);

    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $userids = $userlist->get_userids();

        $DB->delete_records_list('block_vxg_orgs_right', 'userid', $userids);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel == CONTEXT_USER && $context->instanceid == $userid) {
                $DB->delete_records('block_vxg_orgs_right', ['userid' => $userid]);
                break;
            }
        }
    }
}
