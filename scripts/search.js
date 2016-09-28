/**
 * Created by mattias.nording on 2016-09-28.
 */
$("#order-type").on('change',function()
{
    var value = $('#order-type option:selected' ).val();
    var fromdate = $('#fromdate').val();
    var todate = $('#todate').val();

    $.get("api/orders/"+value+"/from/"+fromdate+"/to/"+todate,function(data)
    {
        console.log(data);
        $(".orderlist").html("");
            $.each(data,function(i,item)
            {
                console.log(item);
                $(".orderlist").append('<div class="row">'+
                '<div class="small-4 columns"><span>'+item.reservation+'</span></div>'+
                '<div class="small-4 columns"><span>'+item.status+'</span></div>'+
                '<div class="small-4 columns"></div>'+
                '</div>');
            });
    })
});