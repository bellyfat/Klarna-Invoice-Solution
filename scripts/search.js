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
        populateData(data);
    });

});
function populateData(data)
{
    console.log(data);
    $(".orderlist").html("");
    $.each(data,function(i,item)
    {
        console.log(item);
        $(".orderlist").append('<div class="row">'+
            '<div class="small-3 columns"><a href="orderview.php?id='+item.id+'">'+item.reservation+'</a></div>'+
            '<div class="small-3 columns"><span>'+item.status+'</span></div>'+
            '<div class="small-2 columns"><span>'+item.sum+'</span></div>'+
            '<div class="small-4 columns"><span>'+item.datetime+'</span></div>'+
            '</div>');
    });
}