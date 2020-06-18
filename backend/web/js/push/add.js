$(function() {
    $(document).on('click', '#calc-set-push', function() {
        var $this = $(this);
        var container = $('#calc-value');
        var url = $this.data('url');
        var registrationDataRange = $('[name="SetUserPushRecord\[registrationDataRange\]"]').val();
        var deeplink_id = $('#deeplink_id').val();
        var gtest_id = $('#gtest_id').val();
        var wtest_id = $('#wtest_id').val();

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            data: {'registrationDataRange': registrationDataRange, 'deeplink_id': deeplink_id, 'gtest_id': gtest_id, 'wtest_id': wtest_id},
            success: function (json) {
                if (json.result == 'success') {
                    container.html(json.count);
                } else {
                    alert('Произошла ошибка при расчете количества пользователей\nПопробуйте еще раз');
                }
            }
        });
    })
});