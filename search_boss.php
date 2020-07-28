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

require_once $CFG->dirroot . '/user/filters/lib.php';

class user_filter_search_boss extends user_filter_type
{

    public function __construct($name, $label, $advanced)
    {
        parent::__construct($name, $label, $advanced);
    }

    public function get_operators()
    {
        return array(1 => get_string('isanyvalue', 'filters'),
            // 2              => get_string('isequalto', 'filters')
        );
    }

    public function setupForm(&$mform)
    {
        $name = $this->_name;

        $objs           = array();
        $objs['select'] = $mform->createElement('select', $name . '_op', null, $this->get_operators());
        $objs['select']->setLabel(get_string('limiterfor', 'filters', $this->_label));

        $options = array('ajax' => 'block_vxg_positions/auto_positions', 'multiple' => true);

        $objs['search'] = $mform->createElement('autocomplete', $name . '_s', '', array(), $options);

        $mform->addElement('group', $name . '_grp', $this->_label, $objs, '', false);

        if ($this->_advanced) {
            $mform->setAdvanced($name.'_grp');
        }
    }

    public function get_sql_filter($data)
    {
        global $DB;

        if ($data['operator'] == 1) {
            $operator = 'OR';
        } else if ($data['operator'] == 2) {
            $operator = 'AND';
        }

        $values    = (array) $data['value'];

        $childids = array();
        $positions = $DB->get_records_list('vxg_pos', 'parentid', $values);
        foreach ($positions as $position) {
            $childids[] = $position->id;
        }
        $childs = $DB->get_records_list('vxg_pos', 'id', $childids);

        if (!empty($childs)) {
            $posids = array();
            foreach ($childs as $child) {
                $posids[] = $child->id;
            }


            // if no positon found means no user has this position assign either
            $where_list = implode(" $operator up.posid = ", $posids);
        } else {
            $where_list = 0;
        }

        $sql = "id IN (SELECT userid
        FROM {vxg_user_pos} up
        WHERE up.posid = $where_list)";



        return array($sql, array());
    }

    public function check_data($formdata)
    {

        $field    = $this->_name . '_s';
        $operator = $this->_name . '_op';

        if (isset($formdata->$field) and !empty($formdata->$field)) {
            // If field value is set then use it, else it's null.
            $fieldvalue = null;
            if (isset($formdata->$field) and !empty($formdata->$field)) {
                $fieldvalue = $formdata->$field;
            }
            // return false;
            return array('operator' => (int) $formdata->$operator, 'value' => $fieldvalue);
        }
        return false;
    }

    public function get_label($data)
    {
        global $DB;
        $operators = $this->get_operators();
        $operator  = $data['operator'];
        $values    = $data['value'];

        if (empty($operator)) {
            return '';
        }

        if (is_array($values)) {
            $items = $DB->get_records_list('vxg_pos', 'id', $values, '', 'id, fullname');
        } else {
            $items = $DB->get_field('vxg_pos', 'id, fullname', array('id' => $values));
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
