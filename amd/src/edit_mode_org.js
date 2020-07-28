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


define(["jquery", "core/config"], function($, config) {
  return {
    init: function() {
      $("#orgs_tree .tree_item").click(function() {
        $('#orgs_tree [aria-selected="true"]').attr("aria-selected", "false");
      });

      $("#vxg_search_orgs")
        .parent()
        .on("click", "[role=option]", function(e) {
          var orgid = $(this).attr("data-value");
          $("#orgs_tree [aria-selected=true]").attr("aria-selected", "false");
          var treeitem = $("#orgs_tree [data-orgid=" + orgid + "]");
          treeitem.attr("aria-selected", "true");
          treeitem.parents().attr("aria-hidden", "false");
          treeitem
            .parents()
            .siblings('[aria-expanded="false"]')
            .attr("aria-expanded", "true");
        });
      // Add org admin
      orgadminnew_a = $("#vxg_org_addadmin_btn");
      var orgadminnew_url_start = orgadminnew_a.attr("href");

      var selected = $('[aria-selected="true"]');
      var org = $("#orgs_tree").find(".tree_item");

      var admin_url = orgadminnew_url_start + "&orgid=0";
      orgadminnew_a.attr("href", admin_url);

      var org_id = "";
      org.click(function() {
        org_id = $(this).attr("data-orgid");
        make_add_orgadmin_url(org_id, orgadminnew_a, orgadminnew_url_start);
      });
      // Add
      orgnew_a = $("#vxg_org_new_btn");
      var orgnew_url_start = orgnew_a.attr("href");

      var selected = $('[aria-selected="true"]');
      var org = $("#orgs_tree").find(".tree_item");

      var new_url = orgnew_url_start + "&orgid=0";
      orgnew_a.attr("href", new_url);

      var org_id = "";
      org.click(function() {
        org_id = $(this).attr("data-orgid");
        make_add_org_url(org_id, orgnew_a, orgnew_url_start);
      });
      // Edit
      orgedit_a = $("#vxg_org_edit_btn");

      var orgedit_url_start = orgedit_a.attr("href");

      var selected = $('[aria-selected="true"]');
      var org = $("#orgs_tree").find(".tree_item");

      var org_id = "";
      org.click(function() {
        org_id = $(this).attr("data-orgid");
        make_edit_org_url(org_id, orgedit_a, orgedit_url_start);
      });
      // del
      orgdel_a = $("#vxg_org_del_btn");
      var orgdel_url_start = orgdel_a.attr("href");

      var selected = $('[aria-selected="true"]');
      var org = $("#orgs_tree").find(".tree_item");

      var org_id = "";
      org.click(function() {
        org_id = $(this).attr("data-orgid");
        make_delete_org_url(org_id, orgdel_a, orgdel_url_start);
      });

      var toggle_search_btn = $(".vxg-search-org-btn");
      toggle_search_btn.click(function() {
        // toggle search hide/show
        $("#vxg_search_orgs").toggleClass("vxg_search_orgs");

        // Add click event to li elements in the suggestions list
        var suggestionsElement = $("#vxg_search_orgs");
        suggestionsElement.parent().on("click", "[role=option]", function(e) {
          var element = $(e.currentTarget).closest("[role=option]");
          var org_id = element.attr("data-value");
          
          make_add_orgadmin_url(org_id, orgadminnew_a, orgadminnew_url_start);
          make_add_org_url(org_id, orgnew_a, orgnew_url_start);
          make_delete_org_url(org_id, orgdel_a, orgdel_url_start);
          make_edit_org_url(org_id, orgedit_a, orgedit_url_start);
        });
      });
    }
  };

  function make_delete_org_url(org_id, orgdel_a, orgdel_url_start) {
    if (org_id != 0) {
      del_url = orgdel_url_start + "&orgid=" + org_id;
      orgdel_a.attr("href", del_url);
      orgdel_a.children().removeAttr("disabled");
      orgdel_a.children().css("pointer-events", "initial");
    } else {
      orgdel_a.children().attr("disabled", true);
      orgdel_a.children().css("pointer-events", "none");
    }
  }

  function make_edit_org_url(org_id, orgedit_a, orgedit_url_start) {
    if (org_id != 0) {
      edit_url = orgedit_url_start + "&orgid=" + org_id;
      orgedit_a.attr("href", edit_url);
      orgedit_a.children().removeAttr("disabled");
      orgedit_a.children().css("pointer-events", "initial");
    } else {
      orgedit_a.children().attr("disabled", true);
      orgedit_a.children().css("pointer-events", "none");
    }
  }

  function make_add_org_url(org_id, orgnew_a, orgnew_url_start) {
    new_url = orgnew_url_start + "&orgid=" + org_id;
    orgnew_a.attr("href", new_url);
    orgnew_a.children().removeAttr("disabled");
    orgnew_a.children().css("pointer-events", "initial");
  }

  function make_add_orgadmin_url(org_id, orgadminnew_a, orgadminnew_url_start) {
    if (org_id != 0) {
    admin_url = orgadminnew_url_start + "&orgid=" + org_id;
    orgadminnew_a.attr("href", admin_url);
    orgadminnew_a.children().removeAttr("disabled");
    orgadminnew_a.children().css("pointer-events", "initial");
    } else {
      orgadminnew_a.children().attr("disabled", true);
      orgadminnew_a.children().css("pointer-events", "none");
    }
  }
});
