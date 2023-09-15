/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/schedule
 */

(function($) {
    /** global: Craft */
    /** global: Garnish */
    const Timer = Garnish.Base.extend(
        {
            $groups: null,
            $selectedGroup: null,

            init() {
                $('#content .lightswitch').on('change', function() {
                    const enabled = $(this).data('lightswitch').on;
                    const data = {
                        id: $(this).closest('tr').data('id'),
                        enabled: enabled ? '1' : '0'
                    };
                    Craft.postActionRequest('schedule/timers/toggle-timer', data, (response, textStatus, jqXHR) => {
                        if (textStatus === 'success' && response.success) {
                            Craft.cp.displayNotice(enabled ? Craft.t('schedule', 'Timer enabled.') : Craft.t('schedule', 'Timer disabled.'));
                        } else {
                            Craft.cp.displayError(Craft.t('app', 'An unknown error occurred.'));
                        }
                    });
                });
            },
        });


    Garnish.$doc.ready(() => {
        new Timer();
    });
})(jQuery);
