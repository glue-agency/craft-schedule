/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/panlatent/schedule
 */

(function($) {
    /** global: Craft */
    /** global: Garnish */
    var Timer = Garnish.Base.extend(
        {
            init: function() {
                $('#content .lightswitch').on('change', function() {
                    var enabled = $(this).data('lightswitch').on;
                    var data = {
                        id: $(this).closest('tr').data('id'),
                        enabled: enabled ? '1' : '0'
                    };
                    Craft.postActionRequest('schedule/timers/toggle-timer', data, function(response, textStatus, jqXHR) {
                        if (textStatus === 'success' && response.success) {
                            Craft.cp.displayNotice(enabled ? Craft.t('schedule', 'Timer enabled.') : Craft.t('schedule', 'Timer disabled.'));
                        } else {
                            Craft.cp.displayError(Craft.t('app', 'An unknown error occurred.'));
                        }
                    });
                });
            },
        });

    Garnish.$doc.ready(function() {
        new Timer();
    });
})(jQuery);
