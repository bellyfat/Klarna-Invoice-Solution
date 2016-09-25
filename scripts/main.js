var CustomerAddresses;
$('#getadress').on('click',function()
{
    //$('#getadress').toggleClass('is-disabled');
    var pno  = $('#pno').val();
    var selectedstore = $("#purchasestore").val();
    $.get('api/'+selectedstore+'/adress/'+pno,function(data)
    {
        console.log(data);
        CustomerAddresses = data;
        setAdresses(CustomerAddresses[0]);
            $.each(data, function (i, item) {
                $('#adresses').append($('<option>', {
                    value: i,
                    text : item.firstname+' '+item.lastname+' '+item.street
                }));
            });

        })
});
$('#buy').on('click',function(e)
{
    e.preventDefault();
    var selectedstore = $("#purchasestore").val();
    //$(this).addClass("is-disabled");
    $.post('api/'+selectedstore+'/buy', $('#buyForm').serialize(),function(data)
    {
        if(data.invno)
        {
            $('.cui__dialog__overlay').addClass('is-visible');
            $('#invoiceLabel').html(data.invno);
            $('#amountlabel').html(data.amount+' KR');
        }
    })
})
$('#adresses').on('change',function()
{
    var value = $('#adresses option:selected' ).val();
    setAdresses(CustomerAddresses[value]);

})
function setAdresses(adress)
{
    $('#buy').removeClass('is-disabled');
    $('#selectedCustomer').html(adress.street+' '+adress.postal+' '+adress.city);
    $('#custname').val(adress.firstname)
    $('#custsurname').val(adress.lastname)
    $('#custstreet').val(adress.street)
    $('#custpostal').val(adress.postal)
    $('#custcity').val(adress.city)
}
$('#addNewProd').on('click',function()
{
var copy = $('#orderLine').html();
    $('#orderLine').append(copy);
});
 $('input').focus(function() {
        $(this).parent().addClass('is-focused');
    })
    $('input').blur(function() {
        $(this).parent().removeClass('is-focused');
    })
    function setFilled(_, input) {
        if ($(input).val()) {
            $(input).parent().addClass('is-filled');
        } else {
            $(input).parent().removeClass('is-filled');
        }
    }
    $('input').keyup(function(event) {
        setFilled(null , event.target)
    })
    $('input').change(function(event) {
        setFilled(null , event.target)
    })
    $('input').each(setFilled)
    $('.cui__switch--multiple__option, .cui__switch').mousedown(function() {
        $(this).addClass('is-pressed')
    })
    $('.cui__switch--multiple__option, .cui__switch').mouseup(function() {
        $(this).removeClass('is-pressed')
    })
    $('.cui__switch--multiple__option, .cui__switch').click(function() {
        $(this).toggleClass('is-checked');
    })
    $('.cui__dropdown--selector__option, .cui__dropdown--radio__option').click(function() {
        $(this).toggleClass('is-selected');
    })
    $('.cui__dropdown--custom__option').click(function() {
        if ($(this).parent().hasClass('is-collapsed')) {
            $(this).parent().removeClass('is-collapsed');
        } else {
            $(this).parent().children().removeClass('is-selected');
            $(this).toggleClass('is-selected');
            $(this).parent().addClass('is-collapsed');
        }
    })
$('svg').click(function () {
    $('.cui__dialog__overlay').removeClass('is-visible');
    window.location.reload();
});
$('#paymentmethods').on('change',function()
{
    var value = $('#paymentmethods option:selected' ).html();
    $('#selectedMethod').html(value);
});
$(document).ready(function()
{
    $.get('api/methods',function(data)
    {
        $.each(data.payment_methods, function (i, item) {
            $('#paymentmethods').append($('<option>', {
                value: item.pclass_id,
                text : item.name+' '+item.title
            }));
        });
    })
})
