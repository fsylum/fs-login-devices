(function ($) {
    $(document).ready(function () {
        $('.js-delete-login-device').on('click', function (e) {
            e.preventDefault();

            if (window.confirm('This entry will be permanently deleted and cannot be recovered. Are you sure?')) {
                window.location = this.href;
            };
        });

        $('#doaction, #doaction2').on('click', function (e) {
            e.preventDefault();

            if (window.confirm('The selected entries will be permanently deleted and cannot be recovered. Are you sure?')) {
                $(this).closest('form').trigger('submit');
            };
        });

        let loginStartDatepicker = $('#filter-login-start-date').datepicker({
            nextText: '&rsaquo;',
            prevText: '&lsaquo;'
        });

        let loginEndDatepicker = $('#filter-login-end-date').datepicker({
            nextText: '&rsaquo;',
            prevText: '&lsaquo;'
        });

        let logoutStartDatepicker = $('#filter-logout-start-date').datepicker({
            nextText: '&rsaquo;',
            prevText: '&lsaquo;'
        });

        let logoutEndDatepicker = $('#filter-logout-end-date').datepicker({
            nextText: '&rsaquo;',
            prevText: '&lsaquo;'
        });

        loginStartDatepicker.on('change', function () {
            loginEndDatepicker.datepicker('option', 'minDate', loginStartDatepicker.datepicker('getDate'));
        });

        loginEndDatepicker.on('change', function () {
            loginStartDatepicker.datepicker('option', 'maxDate', loginEndDatepicker.datepicker('getDate'));
        });

        logoutStartDatepicker.on('change', function () {
            logoutEndDatepicker.datepicker('option', 'minDate', logoutStartDatepicker.datepicker('getDate'));
        });

        logoutEndDatepicker.on('change', function () {
            logoutStartDatepicker.datepicker('option', 'maxDate', logoutEndDatepicker.datepicker('getDate'));
        });
    });
})(jQuery);
