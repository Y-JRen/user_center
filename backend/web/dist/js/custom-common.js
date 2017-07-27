(function ($, AdminLTE) {

  "use strict";
  //全选
    $(".table-list-check thead th input[type='checkbox']").click(function(){
        if($(this).prop("checked")){
          $(this).prop("checked",true);
          $(this).parents("thead").next().find("tr").each(function(){
             $(this).find("td").eq(0).find("input[type='checkbox']").prop("checked",true);
          });
        }else{
          $(this).prop("checked",false);
          $(this).parents("thead").next().find("tr").each(function(){
             $(this).find("td").eq(0).find("input[type='checkbox']").prop("checked",false);
          });
        }
    });

    //单个全选或取消全选
    $(".table-list-check tbody tr td input[type='checkbox']").click(function(){
        var bol = false;
        var trnum =$(this).parents("tbody").find("tr input[type='checkbox']").length;
        var checkednum =$(this).parents("tbody").find("tr :checked").length;
          if( trnum == checkednum){
              $(".table-list-check thead th input[type='checkbox']").prop("checked",true);
          }else{
              $(".table-list-check thead th input[type='checkbox']").prop("checked",false);
          }
    });

    /*筛选*/
    $("body").delegate(".lte-table-filter-dropdown .lte-table-filter-dropdown-btns .confirm","click",function(){
          $("form").submit();
    });

    $("body").delegate(".lte-table-filter-dropdown .lte-table-filter-dropdown-btns .clean","click",function(){
        $(this).parents(".lte-table-filter-dropdown").find(":checkbox").prop("checked",false);
        $(".lte-filterbox .fa-filter").removeClass("c-blue");
    });

    $("body").delegate(".lte-filterbox .fa-filter","click",function(){
        var contex = $(this).parents(".lte-filterbox").find(".lte-table-filter-dropdown");
            if(contex.hasClass("none")){
              contex.removeClass("none");
            }else{
              contex.addClass("none");
            }
    });


    /*排序*/
    $("body").delegate(".lte-table-column-sorter","click",function(){
        $(this).find("span.on").removeClass("on").addClass("off").siblings().removeClass("off").addClass("on");
        //.removeClass("off").addClass("on").siblings().removeClass("on").addClass("off");
    });

    $(document).bind("click",function(e){
      var target  = $(e.target);
          if(target.closest(".lte-filterbox").length == 0){
              $(".lte-table-filter-dropdown").addClass("none");
          };
          e.stopPropagation();
    });

    $(".custom-dropdown ul li a").click(function(){
        var val = $(this).text();
        $(this).parents(".custom-dropdown").find(":hidden").val(val).next().find("span").html(val);
    });
    //导航
   // menu();

})(jQuery, $.AdminLTE);

function cs_datetimepicker(contex,options,format,num){
  var date = new Date();
  var defaults ={
              "opens": "left",
              "autoApply": true,
              "autoUpdateInput":true,
              "dateLimit": {"months": 6},
              "minDate":'2015-12-31',
              "maxDate":date.toLocaleString().split(" ")[0],
              "locale": {
                "format": format,
                'applyLabel' : '确定',
                'cancelLabel' : '取消',
                'daysOfWeek': ['日', '一', '二', '三', '四', '五','六'],
                'monthNames': ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
                'firstDay': 1
              },
      }
  var settings = $.extend({}, defaults , options);
    $(contex).daterangepicker(settings);

    $(contex).on('apply.daterangepicker', function(ev, picker) {
      switch(num){
        case 'single':
          $(contex).val(picker.endDate.format(format));
        break;
        default:
          $(contex).val(picker.startDate.format(format)+"~"+picker.endDate.format(format));
        break;
      }

    });
}


function check_null(a){
  var bol = false;
  var val = $(a).val();
      if(val == ''){
        $(a).addClass("error");
        bol = false;
      }else{
        $(a).removeClass("error");
        bol = true;
      }
      return bol;
}

function check_upload(){
  if($(".uploadlist li").length == 0){
      return false;
  }else{
      return true;
  }
}
