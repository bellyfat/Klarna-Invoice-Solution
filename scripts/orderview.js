/**
 * Created by mattias.nording on 2016-11-08.
 */
function openInvoice(inv)
{
    myWindow=window.open(inv,'','width=800,height=800');
    myWindow.focus();
    myWindow.print(); //DOES NOT WORK

}
function getInvoiceStatus(store,invoice)
{
    $.get("api/"+store+"/invoice/"+invoice,function(data)
    {
        $("#invoiceStatus").html("Is paid: "+data[1]+"</br>Due date: "+data[2]);
    });
}