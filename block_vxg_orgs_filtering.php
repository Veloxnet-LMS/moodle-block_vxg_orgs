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
 * Filter class for the organisation block
 * @package    block_vxg_orgs
 * @copyright  Veloxnet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/user/filters/lib.php');
require_once(__DIR__ . '/search_boss.php');
require_once(__DIR__ . '/search_org_job.php');
require_once(__DIR__ . '/orgadmin_check.php');

/**
 * Organisation block filtering wrapper class.
 *
 * @package    block_vxg_orgs
 * @copyright  Veloxnet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_vxg_orgs_filtering extends user_filtering {

    /**
     * Creates known filter if present
     * @param string $fieldname
     * @param boolean $advanced
     * @return object filter
     */
    public function get_field($fieldname, $advanced) {
        global $USER, $CFG, $DB, $SITE;

        switch ($fieldname) {
            case 'username':
                return new user_filter_text('username', get_string('username'), $advanced, 'username');
            case 'realname':
                return new user_filter_text('realname', get_string('fullnameuser'), $advanced, $DB->sql_fullname());
            case 'lastname':
                return new user_filter_text('lastname', get_string('lastname'), $advanced, 'lastname');
            case 'firstname':
                return new user_filter_text('firstname', get_string('firstname'), $advanced, 'firstname');
            case 'email':
                return new user_filter_text('email', get_string('email'), $advanced, 'email');
            case 'suspended':
                return new user_filter_yesno('suspended', get_string('suspended', 'auth'), $advanced, 'suspended');
            case 'courserole':
                return new user_filter_courserole('courserole', get_string('courserole', 'filters'), $advanced);
            case 'systemrole':
                return new user_filter_globalrole('systemrole', get_string('globalrole', 'role'), $advanced);
            case 'firstaccess':
                return new user_filter_date('firstaccess', get_string('firstaccess', 'filters'), $advanced, 'firstaccess');
            case 'lastaccess':
                return new user_filter_date('lastaccess', get_string('lastaccess'), $advanced, 'lastaccess');
            case 'timemodified':
                return new user_filter_date('timemodified', get_string('lastmodified'), $advanced, 'timemodified');
            case 'idnumber':
                return new user_filter_text('idnumber', get_string('idnumber'), $advanced, 'idnumber');
            case 'org':
                if ($DB->get_manager()->table_exists('vxg_user_pos')) {
                    return new block_vxg_orgs_search_org_job('org', get_string('org', 'block_vxg_orgs'), $advanced, 'org');
                } else {
                    return null;
                }
            case 'job':
                if ($DB->get_manager()->table_exists('vxg_user_pos')) {
                    return new block_vxg_orgs_search_org_job('job', get_string('job', 'block_vxg_orgs'), $advanced, 'job');
                } else {
                    return null;
                }
            case 'boss':
                if ($DB->get_manager()->table_exists('vxg_user_pos')) {
                    return new block_vxg_orgs_search_boss('boss', get_string('boss', 'block_vxg_orgs'), $advanced, 'boss');
                } else {
                    return null;
                }
            case 'orgadmin':
                return new block_vxg_orgs_orgadmin_check('boss', get_string('onlyorgadmin', 'block_vxg_orgs'),
                                                        $advanced, 'orgadmin');
            default:
                return null;
        }
    }
}
