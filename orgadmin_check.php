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

require_once $CFG->dirroot . '/user/filters/checkbox.php';

class user_filter_orgadmin_check extends user_filter_checkbox
{

    /**
     * Retrieves data from the form data
     *
     * @param object $formdata data submited with the form
     * @return mixed array filter data or false when filter not set
     */
    public function check_data($formdata) {
        $field = $this->_name;
        // Check if disable if options are set. if yes then don't add this..
        if (!empty($this->disableelements) && is_array($this->disableelements)) {
            foreach ($this->disableelements as $disableelement) {
                if (property_exists($formdata, $field)) {
                    return false;
                }
            }
        }
        if (property_exists($formdata, $field) and $formdata->$field !== '') {
            return array('value' => (string)$formdata->$field, 'orgid' => $formdata->orgid);
        }
        return false;
    }

        /**
     * Returns the condition to be used with SQL where
     *
     * @param array $data filter settings
     * @return array sql string and $params
     */
    public function get_sql_filter($data) {
        $field  = $this->field;

        $sql = "id IN (SELECT userid
        FROM {vxg_right} r
        WHERE r.objecttype = 'org' AND objectid = $data[orgid])";

        return array($sql, array());
    }

}