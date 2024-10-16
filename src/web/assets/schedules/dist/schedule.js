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
            $schedules: null,
            $selectedSchedules: null,
            $totalSchedules: null,

            init() {
                this.$groups = $('#groups');
                this.$selectedGroup = this.$groups.find('a.sel:first');
                this.$selectAllButton = $('#schedules div.checkbox.checbox-select-all');

                this.$allSchedules = $('#schedules tr[data-id]');
                this.$totalSchedules = this.$allSchedules.length;

                this.actions = $('#content #actions-container');

                $('#schedules div.checkbox:not(.checbox-select-all)').on('click', (el) => {
                    $(el.target).closest('tr').toggleClass('sel');
                    const selectedAmount = $('#schedules tr.sel').length;
                    if (selectedAmount > 0) {
                        if (selectedAmount < this.$totalSchedules) {
                            this.$selectAllButton.removeClass('checked');
                            this.$selectAllButton.addClass('indeterminate');
                        } else {
                            this.$selectAllButton.removeClass('indeterminate');
                            this.$selectAllButton.addClass('checked');
                        }
                    } else {
                        this.$selectAllButton.removeClass('checked');
                        this.$selectAllButton.removeClass('indeterminate');
                    }
                    this.toggleActions(selectedAmount > 0)
                })

                $('#schedules div.checbox-select-all').on('click', (el) => {
                    if ($(el.target).hasClass('checked')) {
                        this.$allSchedules.removeClass('sel');
                        $(el.target).removeClass('indeterminate')
                        $(el.target).removeClass('checked')
                    } else {
                        this.$allSchedules.addClass('sel');
                        $(el.target).removeClass('indeterminate')
                        $(el.target).addClass('checked')
                    }
                    this.toggleActions($(el.target).hasClass('checked'))
                })

                $(document).on('click', '.schedules-set-status[data-param="status"]', this.toggleMultiple);

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

                $('#schedules .lightswitch').on('change', function() {
                    const enabled = $(this).data('lightswitch').on;
                    console.log(enabled)
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

            toggleActions(show) {
                if (show) {
                    this.actions.removeClass('hidden');
                } else {
                    this.actions.addClass('hidden');
                }
            },

            toggleMultiple(e) {
                console.log('test')
                const ids = Array.from($('#schedules tr.sel')).map(schedule => schedule.dataset.id)
                const enabled = e.currentTarget.dataset.value === "enabled";
                const data = {
                    ids,
                    enabled: enabled ? '1' : '0'
                };
                Craft.postActionRequest('schedule/schedules/toggle-multiple-schedules', data, (response, textStatus, jqXHR) => {
                    if (textStatus === 'success' && response.success) {
                        window.location.reload();
                    } else {
                        Craft.cp.displayError(Craft.t('app', 'An unknown error occurred.'));
                    }
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
