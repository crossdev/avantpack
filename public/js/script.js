(function($) {
    $(document).ready(function() {
        $(function() {
            $("form").find('[name="date"]').datepicker({ dateFormat: "yy-mm-dd" });
        });
    });
})(jQuery);

