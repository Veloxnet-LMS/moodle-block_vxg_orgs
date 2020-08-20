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


class block_vxg_orgs_renderer extends plugin_renderer_base {

    public function orgs_tree($orgs, $blockid, $returnurl) {
        $content = '';

        $sortedorgs = $this->sort_orgs($orgs);
        $addurl      = new moodle_url('/blocks/vxg_orgs/add_orgition.php', array(
            'returnurl' => $returnurl));
        $content .= html_writer::start_div('vxg_search_orgs', array('id' => 'vxg_search_orgs'));
        $content .= $this->output->render_from_template('block_vxg_orgs/org_autocomplete_block', array());
        $content .= html_writer::end_div();
        $content .= $this->edit_buttons($returnurl, $blockid);
        $content .= '<ul role="tree" id="orgs_tree" class="block_tree list">';
        $content .= '<li class="contains_branch"><p role="treeitem"
        class="tree_item branch" aria-owns="main_group" aria-expanded="true"
        data-orgid="0"> <span>' . get_string('all_orgs', 'block_vxg_orgs') . '</span> </p>';

        $content .= '<ul role="group" id="main_group" aria-hidden="false">';

        $content .= $this->get_nodes_recursive($sortedorgs, $blockid);
        $content .= '</ul>';
        $content .= '</li>';
        $content .= '</ul>';

        return $content;

    }

    protected function get_nodes_recursive($orgs, $blockid) {
        $content = '';

        foreach ($orgs as $org) {

            $editurl = new moodle_url('/blocks/vxg_orgs/edit_org.php', array(
                'orgid' => $org['id']));

            if (!empty($org['children'])) {
                $content .=
                '<li class="contains_branch">
                <p role="treeitem"
                class="tree_item branch"
                aria-owns="' . $blockid . $org['id'] . '_group"
                data-orgid="' . $org['id'] . '"
                aria-expanded="false"
                aria-selected="false">'
                    . $org['fullname'] . '</p>';
                $content .= '<ul id="' . $blockid . $org['id'] . '_group" aria-hidden="true"  role="group">';
                $content .= $this->get_nodes_recursive($org['children'], $blockid);
                $content .= '</ul>';
                $content .= '</li>';
            } else {
                $content .= '<li role="treeitem"">
                     <p class="tree_item branch" data-orgid="' . $org['id'] . '">
                     ' . $org['fullname'] . '
                     </p></li>';
            }
        }
        return $content;
    }

    /**
     *
     * Make parent child relationships
     *
     * @param array $orgs
     * @return sorted array
     *
     */

    protected function sort_orgs($orgs) {
        $itemsbyreference = array();
        // Build array of item references.
        foreach ($orgs as $key => &$item) {
            $item                          = (array) $item;
            $itemsbyreference[$item['id']] = &$item;
            // Children array.
            $itemsbyreference[$item['id']]['children'] = array();
            // Empty orgs class (so that json_encode adds "orgs: {}" ).
            $itemsbyreference[$item['id']]['orgs'] = new StdClass();
        }
        // Set items as children of the relevant parent item.
        foreach ($orgs as $key => &$item) {
            if ($item['parentid'] && isset($itemsbyreference[$item['parentid']])) {
                $itemsbyreference[$item['parentid']]['children'][] = &$item;
            }
        }

        // Remove items that were added to parents elsewhere.
        foreach ($orgs as $key => &$item) {
            if ($item['parentid'] && isset($itemsbyreference[$item['parentid']])) {
                unset($orgs[$key]);
            }
        }
        return $orgs;
    }

    protected function edit_buttons($returnurl) {
        $buttons = html_writer::start_tag('div', array('class' => 'edit-tree-element-btn'));

        if (has_capability('block/vxg_orgs:caneditorgs', context_system::instance())) {

            $newurl = new moodle_url('/blocks/vxg_orgs/add_org.php', array(
                'returnurl' => $returnurl));
            $editurl = new moodle_url('/blocks/vxg_orgs/edit_org.php', array(
                'returnurl' => $returnurl));
            $delurl = new moodle_url('/blocks/vxg_orgs/delete_org.php', array(
                'returnurl' => $returnurl));

            $newbtn = html_writer::link($newurl,
                html_writer::tag('button', '<span class="icon fa fa-plus-circle"></span>',
                    array('class' => 'btn btn-primary',
                        'data-toggle' => 'tooltip', 'title' => get_string('add_org', 'block_vxg_orgs'))),
                array('id' => 'vxg_org_new_btn'));

            $editbtn = html_writer::link($editurl,
                html_writer::tag('button', '<span class="icon fa fa-edit"></span>',
                array('class' => 'btn btn-info', 'disabled' => '',
                    'data-toggle' => 'tooltip', 'title' => get_string('edit_org_title', 'block_vxg_orgs'))),
                array('style' => 'pointer-events:none;', 'id' => 'vxg_org_edit_btn'));

            $deletebtn = html_writer::link($delurl, html_writer::tag('button', '<span class="icon fa fa-trash"></span>',
                array('class' => 'btn btn-danger', 'disabled' => '',
                    'data-toggle' => 'tooltip', 'title' => get_string('del_org', 'block_vxg_orgs'))),
                array('style' => 'pointer-events:none;', 'id' => 'vxg_org_del_btn'));

            $buttons .= $newbtn;
            $buttons .= $editbtn;
            $buttons .= $deletebtn;
        }

        if (has_capability('block/vxg_orgs:manageorgadmins', context_system::instance())) {
            $addadminurl = new moodle_url('/blocks/vxg_orgs/manage_admins.php', array(
                'returnurl' => $returnurl));
            $addadminbtn = html_writer::link($addadminurl, html_writer::tag('button', '<span class="icon fa fa-user-plus"></span>',
                array('class' => 'btn btn-secondary', 'style' => 'background-color:#ced4da', 'data-toggle' => 'tooltip',
                'title' => get_string('org_admins', 'block_vxg_orgs', ''))),
                array('style' => 'pointer-events:none;', 'id' => 'vxg_org_addadmin_btn'));

            $buttons .= $addadminbtn;
        }

        $buttons .= html_writer::tag('button', '<span class="icon fa fa-search"></span>',
            array('class' => 'vxg-search-org-btn btn btn-primary', 'style' => 'float:right;'));
        $buttons .= html_writer::end_tag('div');

        return $buttons;
    }
}
