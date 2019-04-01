$(document).ready(function(){
    $("#quantity").rangeslider({
        polyfill: true,
        rangeClass: 'custom-range',
        onSlide: function(position, value) {
            updateCalculatedValue(value);
        }
    });
});

/**
 * Update the displayed value for cost/total units
 * @param value
 */
var units = $("#units").val();
var cost = $("#cost").val();
function updateCalculatedValue(value)
{
    $("#calculatedAmount").html(Lang.get("data_usage.topOffTotal", { count: value*units, cost: (cost*value).formatCurrency(_portal.currencySymbol)}));
}