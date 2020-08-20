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


defined ( 'MOODLE_INTERNAL' ) || die ();

require_once($CFG->dirroot . '/lib/externallib.php');

class block_vxg_orgs_external extends external_api {

    public static function get_auto_orgs_parameters() {
        return new external_function_parameters([
            'query' => new external_value(PARAM_TEXT, 'The search query', VALUE_REQUIRED)
        ]);
    }

    public static function get_auto_orgs($query) {
        global $DB;
        $likesql = $DB->sql_like('fullname', ':search', false);
        $params['search'] = "%$query%";
        $where = '(deleted = 0 OR deleted IS null) AND ' . $likesql;

        $orgs = $DB->get_records_select('block_vxg_org', $where, $params, 'fullname', 'id, fullname');
        $orgsoptions = [];
        foreach ($orgs as $org) {
            $orgsoptions[$org->id] = (object)[
                'id' => $org->id,
                'fullname' => $org->fullname,
            ];
        }
        return $orgsoptions;
    }

    public static function get_auto_orgs_returns() {
        return new external_multiple_structure(new external_single_structure(
            [
                'id' => new external_value(PARAM_INT, 'ID of the org'),
                'fullname' => new external_value(PARAM_RAW, 'The name of the org'),
            ]
        ));
    }
}
