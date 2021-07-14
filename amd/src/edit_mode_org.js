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

define(["jquery"], function($) {
    return {
        init: function() {
            $("#orgs_tree .tree_item").click(function() {
                $('#orgs_tree [aria-selected="true"]').attr("aria-selected", "false");
            });

            $("#vxg_search_orgs")
                .parent()
                .on("click", "[role=option]", function() {
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
            // Add org admin.
            var orgadminNewa = $("#vxg_org_addadmin_btn");
            var orgadminNewUrlStart = orgadminNewa.attr("href");

            var org = $("#orgs_tree").find(".tree_item");

            var adminUrl = orgadminNewUrlStart + "&orgid=0";
            orgadminNewa.attr("href", adminUrl);

            var orgId = 0;
            org.click(function() {
                orgId = $(this).attr("data-orgid");
                makeAddOrgadminUrl(orgId, orgadminNewa, orgadminNewUrlStart);
            });
            // Add.
            var orgNewa = $("#vxg_org_new_btn");
            var orgNewUrlStart = orgNewa.attr("href");

            var newUrl = orgNewUrlStart + "&orgid=0";
            orgNewa.attr("href", newUrl);

            org.click(function() {
                orgId = $(this).attr("data-orgid");
                makeAddOrgUrl(orgId, orgNewa, orgNewUrlStart);
            });
            // Edit.
            var orgedita = $("#vxg_org_edit_btn");

            var orgEditUrlStart = orgedita.attr("href");

            org.click(function() {
                orgId = $(this).attr("data-orgid");
                makeEditOrgUrl(orgId, orgedita, orgEditUrlStart);
            });
            // Delete.
            var orgdela = $("#vxg_org_del_btn");
            var orgDelUrlStart = orgdela.attr("href");

            org.click(function() {
                orgId = $(this).attr("data-orgid");
                makeDeleteOrgUrl(orgId, orgdela, orgDelUrlStart);
            });

            var toggleSearchBtn = $(".vxg-search-org-btn");
            toggleSearchBtn.click(function() {
                // Toggle search hide/show.
                $("#vxg_search_orgs").toggleClass("vxg_search_orgs");

                // Add click event to li elements in the suggestions list.
                var suggestionsElement = $("#vxg_search_orgs");
                suggestionsElement.parent().on("click", "[role=option]", function(e) {
                    var element = $(e.currentTarget).closest("[role=option]");
                    var orgId = element.attr("data-value");

                    makeAddOrgadminUrl(orgId, orgadminNewa, orgadminNewUrlStart);
                    makeAddOrgUrl(orgId, orgNewa, orgNewUrlStart);
                    makeDeleteOrgUrl(orgId, orgdela, orgDelUrlStart);
                    makeEditOrgUrl(orgId, orgedita, orgEditUrlStart);
                });
            });
        }
    };

    /**
     * Controls the url for the delete button.
     * @param {int} orgId
     * @param {object} orgdela
     * @param {String} orgDelUrlStart
     */
    function makeDeleteOrgUrl(orgId, orgdela, orgDelUrlStart) {
        if (orgId != 0) {
            var delUrl = orgDelUrlStart + "&orgid=" + orgId;
            orgdela.attr("href", delUrl);
            orgdela.children().removeAttr("disabled");
            orgdela.children().css("pointer-events", "initial");
        } else {
            orgdela.children().attr("disabled", true);
            orgdela.children().css("pointer-events", "none");
        }
    }

    /**
     * Controls the url for the edit button.
     * @param {int} orgId
     * @param {object} orgedita
     * @param {String} orgEditUrlStart
     */
    function makeEditOrgUrl(orgId, orgedita, orgEditUrlStart) {
        if (orgId != 0) {
            var editUrl = orgEditUrlStart + "&orgid=" + orgId;
            orgedita.attr("href", editUrl);
            orgedita.children().removeAttr("disabled");
            orgedita.children().css("pointer-events", "initial");
        } else {
            orgedita.children().attr("disabled", true);
            orgedita.children().css("pointer-events", "none");
        }
    }

    /**
     * Controls the url for the new button.
     * @param {int} orgId
     * @param {object} orgNewa
     * @param {String} orgNewUrlStart
     */
    function makeAddOrgUrl(orgId, orgNewa, orgNewUrlStart) {
        var newUrl = orgNewUrlStart + "&orgid=" + orgId;
        orgNewa.attr("href", newUrl);
        orgNewa.children().removeAttr("disabled");
        orgNewa.children().css("pointer-events", "initial");
    }

    /**
     * Controls the url for the orgadmin button.
     * @param {int} orgId
     * @param {object} orgadminNewa
     * @param {String} orgadminNewUrlStart
     */
    function makeAddOrgadminUrl(orgId, orgadminNewa, orgadminNewUrlStart) {
        if (orgId != 0) {
            var adminUrl = orgadminNewUrlStart + "&orgid=" + orgId;
            orgadminNewa.attr("href", adminUrl);
            orgadminNewa.children().removeAttr("disabled");
            orgadminNewa.children().css("pointer-events", "initial");
        } else {
            orgadminNewa.children().attr("disabled", true);
            orgadminNewa.children().css("pointer-events", "none");
        }
    }
});
