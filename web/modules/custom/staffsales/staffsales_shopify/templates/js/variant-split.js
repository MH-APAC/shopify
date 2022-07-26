(function ($, Drupal) {
    Drupal.behaviors.myModuleBehavior = {
        attach: function (context, settings) {
            $(document).ready(function () {
                $("#edit-select-all").on('click', function (e) {
                    e.preventDefault();
                    $("input[type='checkbox']").each(function (i, element) {
                        $(element).prop("checked", true);
                    });
                    return false;
                })
            });
        }
    };
})(jQuery, Drupal);
