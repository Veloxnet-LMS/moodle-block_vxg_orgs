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
 * Defines the edit organisation form
 * @package    block_vxg_orgs
 * @copyright  Veloxnet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_vxg_orgs\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Delete org moodleform class
 * @copyright  Veloxnet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_orgs_form extends \moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        $mform = $this->_form;

        $data        = $this->_customdata['data'];
        $fileoptions = $this->_customdata['fileoptions'];
        $toform      = $this->_customdata['toform'];

        $mform->addElement('text', 'idnumber', get_string('idnumber', 'block_vxg_orgs'), array('style' => 'width:100%'));
        $mform->setType('idnumber', PARAM_RAW);

        $mform->addElement('text', 'fullname', get_string('fullname', 'block_vxg_orgs'), array('style' => 'width:100%'));
        $mform->setType('fullname', PARAM_RAW);
        $mform->addRule('fullname', null, 'required', null, 'client');

        $mform->addElement('text', 'secondaryname', get_string('secondaryname', 'block_vxg_orgs'), array('style' => 'width:100%'));
        $mform->setType('secondaryname', PARAM_RAW);

        $mform->addElement('textarea', 'description', get_string('description', 'block_vxg_orgs'), array('style' => 'width:100%'));
        $mform->setType('description', PARAM_RAW);

        $mform->addElement('date_selector', 'validfrom', get_string('validfrom', 'block_vxg_orgs'), array('optional' => true));

        $mform->addElement('date_selector', 'validto', get_string('validto', 'block_vxg_orgs'), array('optional' => true));

        $mform->addElement('text', 'orgtype', get_string('orgtype', 'block_vxg_orgs'), array('style' => 'width:100%'));
        $mform->setType('orgtype', PARAM_RAW);

        $mform->addElement('text', 'costcenterid', get_string('costcenterid', 'block_vxg_orgs'), array('style' => 'width:100%'));
        $mform->setType('costcenterid', PARAM_RAW);
        $mform->addRule('costcenterid', null, 'numeric', null, 'client');

        $mform->addElement('filemanager', 'files_filemanager', get_string('file', 'block_vxg_orgs'), null,
            $fileoptions);

        $mform->addElement('hidden', 'orgid', $toform['orgid']);
        $mform->setType('orgid', PARAM_INT);
        $mform->addElement('hidden', 'returnurl', $toform['returnurl']);
        $mform->setType('returnurl', PARAM_LOCALURL);

        $this->add_action_buttons();

        $this->set_data($data);
    }
}
