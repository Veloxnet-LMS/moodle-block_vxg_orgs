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
 * Settings for the organisation block
 * @package    block_vxg_orgs
 * @copyright  Veloxnet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once(__DIR__ . '/locallib.php');

$roles = block_vxg_orgs_get_assignable_roles();

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configmulticheckbox('block_vxg_orgs/canadd', get_string('canaddroles', 'block_vxg_orgs'),
    get_string('canadd', 'block_vxg_orgs'), array('manager' => 'manager'), $roles));
}
