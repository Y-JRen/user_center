/**
 * 加载付款账户
 * @returns
 */
function createAccounts() {
    var orgId = $("#org_id").val();
    if (!orgId) {
        return;
    }
    jQuery.ajax({
        type: "GET",
        url: getAccountUrl,
        data: {
            orgId: orgId
        },
        dataType: "json",
        success: function (result) {
            if (result.success) {
                var list = result.data;
                var htmls = '<option value="" >请选择</option>';
                if (list.length > 0) {
                    for (var i = 0; i < list.length; i++) {
                        htmls += '<option value="';
                        htmls += list[i].id;
                        htmls += '" data-number="' + list[i].number + '">';
                        htmls += list[i].name_short;
                        htmls += '</option>';
                    }
                }
            }
            jQuery("#account_id").html(htmls);
        },
        error: function (XMLHttpRequest, timeout, errorThrown) {
            alert("付款账号加载失败");
        }
    });
}

function createFinType(parentTypeId, level, afterCreateCallback) {
    $.ajax({
        url: getTypeUrl,
        data: {parentId: parentTypeId},
        dataType: "json",
        success: function (resp) {
            if (resp.length == 0) return false;
            var children = resp;
            var options = '<option value="">请选择</option>';
            for (var i in children) {
                var type = children[i];
                options += '<option value="' + type.id + '" index="' + i + '">' + type.name + '</option>';
            }
            options = '<select level="' + level + '" id="finType_' + level + '" class="form-control finType" style="margin-bottom:4px;">' + options + '</select>';
            $("#finTypeDiv").append(options);
            removeHighLevelSel(level);
            setTimeout(function () {
                afterCreateCallback && afterCreateCallback();
            }, 0);
        }
    });
}


function removeHighLevelSel(currlevel) {
    $("#finTypeDiv select").each(function () {
        var level = $(this).attr("level");
        if (parseInt(level) > currlevel) {
            $(this).remove();
        }
    });
}


function validFinType() {
    var valid = true;
    $("#finTypeDiv select").each(function () {
        if ($(this).val() == "") {
            valid = false;
            return false;
        }
    });
    return valid;
}


$(function () {
    $("body").on("change", "#finTypeDiv select", function () {
        var val = $(this).val();
        var level = $(this).attr("level");
        if (!val) {
            removeHighLevelSel(level);
            return false;
        }
        level = parseInt(level) + 1;
        $("#finType_" + level).remove();
        createFinType(val, level);
        var option = $(this).find(":selected");
        $("#type_id").val(option.val());
        $("#type").val(option.text());
    });

    $("body").on("change", "#account_id", function () {
        $("#account").val($('#account_id option:selected').attr('data-number'));
    });
});