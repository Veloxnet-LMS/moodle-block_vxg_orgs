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
 * This file contains the Organizations block.
 * @package    block_vxg_orgs
 * @copyright  Veloxnet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Organisation block class
 * @package    block_vxg_orgs
 * @copyright  Veloxnet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_vxg_orgs extends block_base {

    /** @var bool A switch to indicate whether content has been generated or not. */
    protected $contentgenerated = false;
    /** @var bool|null variable for checking if the block is docked */
    protected $docked = null;

    /**
     * Init.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_vxg_orgs');
    }

    /**
     * Allows the block class to have a say in the user's ability to create new instances of this block.
     * This function has access to the complete page object, the creation related to which is being determined.
     *
     * @param moodle_page $page
     * @return boolean
     */
    public function user_can_addto($page) {
        global $CFG;
        require_once(__DIR__ . '/locallib.php');

        if (is_siteadmin()) {
            return true;
        }

        $config = get_config('block_vxg_orgs');

        if (!empty($config->canadd)) {
            $userroles = block_vxg_orgs_get_user_role_names();

            $canadd = false;
            $canaddroles = $config->canadd;
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

    /**
     * This function is called on your subclass right after an instance is loaded
     * Use this function to act on instance data just after it's loaded and before anything else is done
     * For instance: if your block will have different title's depending on location (site, course, blog, etc)
     */
    public function specialization() {

        if (isset($this->config)) {
            if (empty($this->config->title)) {
                $this->title = get_string('pluginname', 'block_vxg_orgs');
            } else {
                $this->title = $this->config->title;
            }
        }
    }

    /**
     * Can be overridden by the block to prevent the block from being dockable.
     *
     * @return bool
     */
    public function instance_can_be_docked() {
        return (parent::instance_can_be_docked() && (empty($this->config->enabledock) || $this->config->enabledock == 'yes'));
    }

    /**
     * Allows the block to load any JS it requires into the page.
     *
     */
    public function get_required_javascript() {
        parent::get_required_javascript();
        $arguments = array(
            'instanceid' => $this->instance->id,
        );
        $this->page->requires->string_for_js('viewallcourses', 'moodle');
        $this->page->requires->js_call_amd('block_vxg_orgs/treeview', 'init', $arguments);
        $this->page->requires->js_call_amd('block_vxg_orgs/edit_mode_org', 'init', $arguments);
    }

    /**
     * Returns the contents.
     *
     * @return stdClass contents of block
     */
    public function get_content() {
        global $DB;

        if ($this->contentgenerated === true) {
            return true;
        }

        if ($this->content !== null) {
            return $this->content;
        }

        $blockid  = $this->instance->id;

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
                'SELECT id, parentid, level, fullname FROM {block_vxg_orgs} WHERE deleted IS NULL OR deleted = 0');

        } else if ($showdeleted == 1) {
            $orgs = $DB->get_records('block_vxg_orgs', null, '', 'id, parentid, level, fullname');
        } else {
            $orgs = $DB->get_records('block_vxg_orgs', null, '', 'id, parentid, level, fullname');
        }
        if (isloggedin()) {
            $renderer            = $this->page->get_renderer('block_vxg_orgs');
            $this->content->text = $renderer->orgs_tree($orgs, $blockid, $this->page->url);
        }

        $this->contentgenerated = true;
        return true;
    }

    /**
     * Allows the block to be added multiple times to a single page
     * @return boolean
     */
    public function instance_allow_multiple() {
        return false;
    }

    /**
     * This block does contain a configuration settings.
     *
     * @return boolean
     */
    public function has_config() {
        return true;
    }
}
