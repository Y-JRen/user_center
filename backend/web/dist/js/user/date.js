$(function () {
    var date = new Date();

    var config = {
        "opens": "left",
        "autoApply": true,
        "autoUpdateInput": false,
        "dateLimit": {"months": 6},
        "minDate": '2016-06-31',
        "maxDate": date.toLocaleString().split(" ")[0],
        "locale": {
            "format": 'YYYY-MM-DD',
            'daysOfWeek': ['日', '一', '二', '三', '四', '五', '六'],
            'monthNames': ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
            'firstDay': 1
        }
    };

    //创建日期
    if ($('#created_at')) {
        $('#created_at').daterangepicker(config);

        //选中
        $('#created_at').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + " - " + picker.endDate.format('YYYY-MM-DD'));
        });
    }

    if ($('#updated_at')) {
        $('#updated_at').daterangepicker(config);

        //选中
        $('#updated_at').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + " - " + picker.endDate.format('YYYY-MM-DD'));
        });
    }
});
