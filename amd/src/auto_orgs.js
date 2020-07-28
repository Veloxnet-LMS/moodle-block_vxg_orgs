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



define(['jquery', 'core/ajax', 'core/templates'], function($, Ajax, Templates) {

    return /** @alias module:block_vxg_orgs/orgs-autocomplete */ {

        processResults: function(selector, results) {
            var orgs = [];
            
            $.each(results, function(index, user) {
                orgs.push({
                    value: user.id,
                    label: user._label
                });
            });
            return orgs;
        },

        transport: function(selector, query, success, failure) {
            var promise;


            promise = Ajax.call([{
                methodname: 'block_vxg_orgs_external_get_auto_org',
                args: {
                    query: query
                }
            }]);

            promise[0].then(function(results) {
                var promises = [],
                    i = 0;

                // Render the label.
                $.each(results, function(index, org) {
                    promises.push(Templates.render('tool_dataprivacy/form-user-selector-suggestion', org));
                });

                // Apply the label to the results.
                return $.when.apply($.when, promises).then(function() {
                    var args = arguments;
                    $.each(results, function(index, user) {
                        user._label = args[i];
                        i++;
                    });
                    success(results);
                    return;
                });

            }).fail(failure);
        }
    };
});
