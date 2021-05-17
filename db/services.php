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
 * Webservice definitions for this plugin
 * @package    block_vxg_orgs
 * @copyright  Veloxnet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$functions = array(

    'block_vxg_orgs_external_get_auto_org' => array(
        'classname'     => 'block_vxg_orgs_external',
        'methodname'    => 'get_auto_orgs',
        'classpath'     => 'blocks/vxg_orgs/classes/externallib.php',
        'description'   => 'Fetches a list of orgs for autocomplete',
        'type'          => 'read',
        'ajax'          => true,
        'loginrequired' => true,
    ),
);
