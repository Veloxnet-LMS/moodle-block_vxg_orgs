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
 * Defines the delete organisation form
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
class delete_orgs_form extends \moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        $mform = $this->_form;

        $orgdata   = $this->_customdata['orgdata'];
        $returnurl = $this->_customdata['returnurl'];

        $mform->addElement('static', 'delete_feedback', get_string('delete_feedback', 'block_vxg_orgs', $orgdata));

        $mform->addElement('hidden', 'orgid', '0');
        $mform->setType('orgid', PARAM_INT);
        $mform->addElement('hidden', 'returnurl', $returnurl);
        $mform->setType('returnurl', PARAM_LOCALURL);

        $this->add_action_buttons(true, get_string('del_org', 'block_vxg_orgs'));
    }
}
