/**
 * Created By: Gregory Benner
 * Date: 8/25/13
 * Time: 7:36 PM
 */
(function(){
    /* DOM Loaded */
    $(function(){
        //Attach All Handlers
        attachHandlers();

        //Load the first element
        loadViewport($('.Item.Selected').data('linkname'));
    });

    function attachHandlers(){
        var $adminPage = $('#Admin.Page');
        $adminPage.find('#SaveGlobalSettings').on('click', saveGlobalSettings);
        $adminPage.find('.ItemsTable')
            .on('click', '.SaveItem', saveItem)
            .on('click', '.DeleteItem', deleteItem);
        $adminPage.find('table#Coupons')
            .on('click', '.Add', addCoupon)
            .on('click', '.Save', updateCoupon)
            .on('click', '.Delete', deleteCoupon);

        $('.ItemsContainer').on('click', '.Item:not(.Selected)', itemClicked);

        $('#Viewport').on('click', '.Genders INPUT[type=radio]', genderClicked)
            .on('click', '#AddToCart', addToCart);

        $('#CartButton').on('click', showCart);


        $('body')
            .on('click', '#Cart #EmptyCart', emptyCart)
            .on('click', '#Cart Button.Update', updateItem)
            .on('click', '#Cart Button.Remove', removeItem)
            .on('click', '#Cart #CloseCart', hideCart)
            .on('click', '#Cart #RemoveCoupon', removeCouponFromCart)
            .on('click', '#Cart #SubmitCoupon', addCouponToCart)
            .on('keyup', '#Cart #CouponsContainer #CouponCode', function(event){
                if(event.keyCode == 13){
                    addCouponToCart(event)
                }
            });
    }

    /* Admin Page Functions */
    function saveGlobalSettings(event){
        var $row = $(event.target).closest('TR');

        var data = {};
        data['StartingDisplayDate'] = $row.find('#StartingDisplayDate').val();
        data['Retail'] = $row.find('#Retail').val();
        data['SalesLimit'] = $row.find('#SalesLimit').val();
        data['DaysApart'] = $row.find('#DaysApart').val();
        data['Level1'] = $row.find('#Level1').val();
        data['Level2'] = $row.find('#Level2').val();
        data['Level3'] = $row.find('#Level3').val();
        data['CartCallout'] = $row.parent().find('#CartCallout').val();

        sendCommand('/Admin', 'SaveGlobals', data);
    }

    function saveItem(event){
        var $row = $(event.target).closest('TR');

        var data = {};
        data['ID'] = $row.data('id');
        data['Name'] = $row.find('#Name').val();
        data['Description'] = $row.find('#Description').val();
        data['Retail'] = $row.find('#Retail').val();
        data['DisplayDate'] = $row.find('#DisplayDate').val();
        data['Votes'] = $row.find('#Votes').val();
        data['Sold'] = $row.find('#Sold').val();
        data['SalesLimit'] = $row.find('#SalesLimit').val();

        sendCommand('/Admin', 'SaveItem', data);
    }

    function deleteItem(event){
        var $row = $(event.target).closest('TR');

        var data = {'ID': $row.data('id')};
        var name = $row.find('#Name').val();

        var okay = confirm('Are you sure you want to delete ' + name + '?');

        if(okay){
            sendCommand('/Admin', 'DeleteItem', data);
        }
    }

    function addCoupon(event){
        var $this = $(event.target).closest('TR');

        var data = {
            'Code':          $this.find('.Code').find('input').val(),
            'IsPercent':     $this.find('.IsPercent').find('input[type=checkbox]:checked').attr('type') == 'checkbox',
            'Amount':        $this.find('.Amount').find('input').val(),
            'UsesRemaining': $this.find('.UsesRemaining').find('input').val()
        };

        sendCommand('/Admin', 'AddCoupon', data);
    }

    function updateCoupon(event){
        var $this = $(event.target).closest('TR');

        var data = {
            'Code':          $this.data('code'),
            'IsPercent':     $this.find('.IsPercent').find('input[type=checkbox]:checked').attr('type') == 'checkbox',
            'Amount':        $this.find('.Amount').find('input').val(),
            'UsesRemaining': $this.find('.UsesRemaining').find('input').val()
        };

        sendCommand('/Admin', 'UpdateCoupon', data);
    }

    function deleteCoupon(event){
        var $this = $(event.target).closest('TR');

        var data = {
            'Code': $this.data('code')
        };

        sendCommand('/Admin', 'DeleteCoupon', data);
    }


    /* Viewport Functions */
    function itemClicked(event){
        var $this = $(event.target).closest('.Item');

        /* Remove all .Selected classes in the ItemsContainer */
        $this.parent().find('.Item').removeClass('Selected');

        /* Add the selected class to the clicked element */
        $this.addClass('Selected');

        /* Load the viewport */
        loadViewport($this.data('linkname'));
    }

    function genderClicked(event){
        var $this = $(event.target);
        var $innerViewport = $this.closest('#InnerViewport');

        var gender = $this.attr('id');
        var name = $innerViewport.data('url');
        var id = $innerViewport.data(gender + 'id');

        loadViewport(name, id);
    }

    function loadViewport(name, id){
        id = id || 0;

        var url = '/Viewport/' + name;

        if(id != 0){
            url += '/' + id;
        }

        $.get(url, function(data){
            $('#Viewport').html(data);
        });
    }

    function addToCart(event){
        var $this = $(event.target).closest('#InnerViewport');

        var gender = $this.find('.Genders').find('Input[type=radio]:checked').attr('ID').toLowerCase();

        var data = {
            'Command': 'AddItem',
            'ID':      $this.data(gender + 'id'),
            'Size':    $this.find('.Sizes').find('Input[type=radio]:checked').val()
        };

        if(data['Size'] == undefined){
            alert('You need to select a size!');
        }else{
            $.post('/Cart', data, function(){
                showCart();
            })
        }
    }


    /* Cart Functions */
    function emptyCart(){

        $('#Cart').find('TR:not(:first-child)').fadeOut(200, function(){
            $(this).remove();
        });

        $.post('/Cart', {'Command': 'EmptyCart'}, function(data){
            $('#Cart').find('#SubTotal').find('span').html(data);
        });
    }

    function updateItem(event){
        var $this = $(event.target).closest('TR');

        var data = {
            'Command':  'UpdateItem',
            'ID':       $this.data('id'),
            'Size':     $this.data('size'),
            'Quantity': $this.find('Input.Quantity').val()
        };

        $.post('/Cart', data, function(data){
            $this.closest('#Cart').find('#SubTotal').find('span').html(data);
        });
    }

    function removeItem(event){
        var $this = $(event.target).closest('TR');

        var data = {
            'Command': 'RemoveItem',
            'ID':      $this.data('id'),
            'Size':    $this.data('size')
        };


        $this.fadeOut(200, function(){
            $this.remove();
        });

        $.post('/Cart', data, function(data){
            $this.closest('#Cart').find('#SubTotal').find('span').html(data);
        });
    }

    function addCouponToCart(event){
        var $this = $(event.target).closest('#CouponsContainer');

        var data = {
            'Command': 'AddCouponToCart',
            'Code':    $this.find('#CouponCode').val()
        };

        $.post('/Cart', data, showCart);
    }

    function removeCouponFromCart(event){
        $.post('/Cart', {'Command': 'RemoveCouponFromCart'}, showCart);
    }

    function showCart(){

        /* Hide any other carts that may have gotten their way in here before showing this one */
        hideCart(true);

        $.get('/Cart', function(data){
            /* Create the container in memory, add everything to it, and then fade it in */
            $('<div>')
                .attr('id', 'CartContainer')
                .html(data)
                .append(
                    $('<div>')
                        .attr('id', 'CartBackdrop')
                        .on('click', hideCart)
                )
                .hide()
                .appendTo($('body'))
                .fadeIn(200);
        });
    }

    function hideCart(skipAnimation){
        var $cartContainer = $('#CartContainer');
        if(skipAnimation === true){
            $cartContainer.remove();
        }else{
            $cartContainer.fadeOut(200, function(){
                $(this).remove();
            });
        }
    }

    /* Helper Functions */
    function sendCommand(url, command, data){

        data['Command'] = command;

        $.post(url, data, function(returnData){
            var obj = $.parseJSON(returnData);
            if(obj['command'] == 'refreshPage'){
                location.reload();
            }else if(obj['status'] == 'OK'){
                alert(obj['message']);
            }else if(obj['status'] == 'ERROR'){
                alert('ERROR! ' + obj['message']);
            }else{
                alert('Unknown Error!');
            }
        });
    }
})();