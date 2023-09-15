/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

(function($) {
    /** global: Craft */
    /** global: Garnish */
    const Schedule = Garnish.Base.extend(
        {
            $groups: null,
            $selectedGroup: null,

            init() {
                this.$groups = $('#groups');
                this.$selectedGroup = this.$groups.find('a.sel:first');
                this.addListener($('#newgroupbtn'), 'activate', 'addNewGroup');

                const $groupSettingsBtn = $('#groupsettingsbtn');

                if ($groupSettingsBtn.length) {
                    const menuBtn = $groupSettingsBtn.data('menubtn');

                    menuBtn.settings.onOptionSelect = $.proxy(function(elem) {
                        const action = $(elem).data('action');

                        switch (action) {
                            case 'rename': {
                                this.renameSelectedGroup();
                                break;
                            }
                            case 'delete': {
                                this.deleteSelectedGroup();
                                break;
                            }
                        }
                    }, this);
                }

                $('#content .lightswitch').on('change', function() {
                    const enabled = $(this).data('lightswitch').on;
                    const data = {
                        id: $(this).closest('tr').data('id'),
                        enabled: enabled ? '1' : '0'
                    };
                    Craft.postActionRequest('schedule/schedules/toggle-schedule', data, (response, textStatus, jqXHR) => {
                        if (textStatus === 'success' && response.success) {
                            Craft.cp.displayNotice(enabled ? Craft.t('schedule', 'Schedule enabled.') : Craft.t('schedule', 'Schedule disabled.'));
                        } else {
                            Craft.cp.displayError(Craft.t('app', 'An unknown error occurred.'));
                        }
                    });
                });
            },

            addNewGroup() {
                const name = this.promptForGroupName('');

                if (name) {
                    const data = {
                        name
                    };

                    Craft.postActionRequest('schedule/schedules/save-group', data, $.proxy(function(response, textStatus) {
                        if (textStatus === 'success') {
                            if (response.success) {
                                location.href = Craft.getUrl(`schedule/groups/${  response.group.id}`);
                            }
                            else if (response.errors) {
                                const errors = this.flattenErrors(response.errors);
                                alert(`${Craft.t('app', 'Could not create the group:')  }\n\n${  errors.join("\n")}`);
                            }
                            else {
                                Craft.cp.displayError();
                            }
                        }

                    }, this));
                }
            },

            renameSelectedGroup() {
                const oldName = this.$selectedGroup.text();
                    const newName = this.promptForGroupName(oldName);

                if (newName && newName !== oldName) {
                    const data = {
                        id: this.$selectedGroup.data('id'),
                        name: newName
                    };

                    Craft.postActionRequest('schedule/schedules/save-group', data, $.proxy(function(response, textStatus) {
                        if (textStatus === 'success') {
                            if (response.success) {
                                this.$selectedGroup.text(response.group.name);
                                Craft.cp.displayNotice(Craft.t('app', 'Group renamed.'));
                            }
                            else if (response.errors) {
                                const errors = this.flattenErrors(response.errors);
                                alert(`${Craft.t('app', 'Could not rename the group:')  }\n\n${  errors.join("\n")}`);
                            }
                            else {
                                Craft.cp.displayError();
                            }
                        }

                    }, this));
                }
            },

            promptForGroupName(oldName) {
                return prompt(Craft.t('app', 'What do you want to name the group?'), oldName);
            },

            deleteSelectedGroup() {
                if (confirm(Craft.t('app', 'Are you sure you want to delete this group?'))) {
                    const data = {
                        id: this.$selectedGroup.data('id')
                    };

                    Craft.postActionRequest('schedule/schedules/delete-group', data, $.proxy((response, textStatus) => {
                        if (textStatus === 'success') {
                            if (response.success) {
                                location.href = Craft.getUrl('schedule');
                            }
                            else {
                                Craft.cp.displayError();
                            }
                        }
                    }, this));
                }
            },

            flattenErrors(responseErrors) {
                let errors = [];

                for (const attribute in responseErrors) {
                    if (!responseErrors.hasOwnProperty(attribute)) {
                        continue;
                    }

                    errors = errors.concat(responseErrors[attribute]);
                }

                return errors;
            }
        });


    Garnish.$doc.ready(() => {
        new Schedule();
    });
})(jQuery);
