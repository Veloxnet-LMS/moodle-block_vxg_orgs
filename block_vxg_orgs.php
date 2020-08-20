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

class block_vxg_orgs extends block_base {

    protected $contentgenerated = false;

    protected $docked = null;

    public function init() {
        $this->title = get_string('pluginname', 'block_vxg_orgs');
    }

    public function user_can_addto($page) {
        global $CFG;
        require_once(__DIR__ . '/locallib.php');

        if (is_siteadmin()) {
            return true;
        }

        $config = get_config('block_vxg_orgs');
        if (!empty($CFG->block_vxg_orgs_canadd)) {
            $userroles = block_vxg_orgs_get_user_role_names();

            $canadd = false;
            $canaddroles = $CFG->block_vxg_orgs_canadd;
            foreach ($userroles as $userrole) {
                if (in_array($userrole, explode(',', $canaddroles))) {
                    return true;
                } else {
                    $canadd = false;
                }
            }
            return $canadd;

        } else {
            if (!has_capability('block/vxg_orgs:addinstance', $page->context)) {
                return false;
            } else {
                return true;
            }
        }
    }

    public function specialization() {

        if (isset($this->config)) {
            if (empty($this->config->title)) {
                $this->title = get_string('pluginname', 'block_vxg_orgs');
            } else {
                $this->title = $this->config->title;
            }
        }
    }

    public function instance_can_be_docked() {
        return (parent::instance_can_be_docked() && (empty($this->config->enabledock) || $this->config->enabledock == 'yes'));
    }

    public function get_required_javascript() {
        parent::get_required_javascript();
        $arguments = array(
            'instanceid' => $this->instance->id,
        );
        $this->page->requires->string_for_js('viewallcourses', 'moodle');
        $this->page->requires->js_call_amd('block_vxg_orgs/treeview', 'init', $arguments);
        $this->page->requires->js_call_amd('block_vxg_orgs/edit_mode_org', 'init', $arguments);
    }

    public function get_content() {
        global $CFG, $OUTPUT, $DB, $COURSE;

        if ($this->contentgenerated === true) {
            return true;
        }

        $blockid  = $this->instance->id;
        $courseid = $COURSE->id;

        $this->content       = new stdClass();
        $this->content->text = '';

        if (isset($this->config)) {
            if (empty($this->config->show_deleted)) {
                $showdeleted = 0;
            } else {
                $showdeleted = $this->config->show_deleted;
            }
        } else {
            $showdeleted = 0;
        }

        if ($showdeleted == 0) {

            $orgs = $DB->get_records_sql(
                'SELECT id, parentid, level, fullname FROM {block_vxg_org} WHERE deleted IS NULL OR deleted = 0');

        } else if ($showdeleted == 1) {
            $orgs = $DB->get_records('block_vxg_org', null, '', 'id, parentid, level, fullname');
        } else {
            $orgs = $DB->get_records('block_vxg_org', null, '', 'id, parentid, level, fullname');
        }
        if (isloggedin()) {
            $renderer            = $this->page->get_renderer('block_vxg_orgs');
            $this->content->text = $renderer->orgs_tree($orgs, $blockid, $this->page->url);
        }

        $this->contentgenerated = true;
        return true;
    }

    public function instance_allow_multiple() {
        return false;
    }

    public function has_config() {
        return true;
    }
}
