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
 * Organisation and job filter
 * @package    block_vxg_orgs
 * @copyright  Veloxnet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/user/filters/lib.php');

/**
 * Filter based on organisations and jobs.
 * @package    block_vxg_orgs
 * @copyright  Veloxnet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_vxg_orgs_search_org_job extends user_filter_type {

    /**
     * Returns an array of comparison operators
     * @return array of comparison operators
     */
    public function get_operators() {
        return array(1 => get_string('isanyvalue', 'filters'),
            2              => get_string('isequalto', 'filters'));
    }

    /**
     * Adds controls specific to this filter in the form.
     * @param object $mform a MoodleForm object to setup
     */
    public function setupform(&$mform) {
        $name = $this->_name;

        $objs           = array();
        $objs['select'] = $mform->createElement('select', $name . '_op', null, $this->get_operators());
        $objs['select']->setLabel(get_string('limiterfor', 'filters', $this->_label));

        $options = array('ajax' => 'block_vxg_' . $name . 's/auto_' . $name . 's', 'multiple' => true);

        $objs['search'] = $mform->createElement('autocomplete', $name . '_s', '', array(), $options);

        $mform->addElement('group', $name . '_grp', $this->_label, $objs, '', false);

        if ($this->_advanced) {
            $mform->setAdvanced($name. '_grp');
        }
    }

    /**
     * Returns the condition to be used with SQL where
     * @param array $data filter settings
     * @return array sql string and $params
     */
    public function get_sql_filter($data) {
        global $DB;

        if ($data['operator'] == 1) {
            $operator = 'OR';
        } else if ($data['operator'] == 2) {
            $operator = 'AND';
        }

        $values    = $data['value'];
        $name = $this->_name . 'id';
        $positions = $DB->get_records_list('vxg_pos', $name, $values);

        if (!empty($positions)) {
            $posids = array();
            foreach ($positions as $position) {
                $posids[] = $position->id;
            }
            // If no positon found means no user has this position assign neither.
            $wherelist = implode(" $operator up.posid = ", $posids);
        } else {
            $wherelist = 0;
        }

        $sql = "id IN (SELECT userid
        FROM {vxg_user_pos} up
        WHERE up.posid = {$wherelist})";

        return array($sql, array());
    }

    /**
     * Retrieves data from the form data
     * @param object $formdata data submited with the form
     * @return mixed array filter data or false when filter not set
     */
    public function check_data($formdata) {
        $field    = $this->_name . '_s';
        $operator = $this->_name . '_op';

        if (isset($formdata->$field) and !empty($formdata->$field)) {
            // If field value is set then use it, else it's null.
            $fieldvalue = null;
            if (isset($formdata->$field) && !empty($formdata->$field)) {
                $fieldvalue = $formdata->$field;
            }
            return array('operator' => (int) $formdata->$operator, 'value' => $fieldvalue);
        }
        return false;
    }

    /**
     * Returns a human friendly description of the filter used as label.
     * @param array $data filter settings
     * @return string active filter label
     */
    public function get_label($data) {
        global $DB;
        $operators = $this->get_operators();
        $operator  = $data['operator'];
        $values    = $data['value'];
        if (empty($operator)) {
            return '';
        }

        $name = 'vxg_' . $this->_name;
        if (is_array($values)) {
            $items = $DB->get_records_list($name, 'id', $values, '', 'id, fullname');
        } else {
            $items = $DB->get_field($name, 'fullname', array('id' => $values));
        }

        $a        = new stdClass();
        $a->label = $this->_label;

        foreach ($items as $item) {
            $itemsarray[] = $item->fullname;
        }
        $items = implode(', ', $itemsarray);

        $a->value    = '"' . $items . '"';
        $a->operator = $operators[$operator];

        return get_string('selectlabel', 'filters', $a);
    }
}
