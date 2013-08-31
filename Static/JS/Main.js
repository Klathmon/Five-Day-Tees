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
        $adminPage.find('.SaveItem').click(saveItem);
        $adminPage.find('.DeleteItem').on('click', deleteItem);

        $('.ItemsContainer').on('click', '.Item:not(.Selected)', itemClicked);
        $('#Viewport').on('click', '.Genders INPUT[type=radio]', genderClicked);
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


    /* Viewport Stuff */
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


    /* Helper Functions */
    function sendCommand(url, command, data){

        data['Command'] = command;

        $.post(url, data, function(returnData){
            obj = $.parseJSON(returnData);
            if(obj['status'] == 'OK'){
                alert(obj['message']);
            }else if(obj['status'] == 'ERROR'){
                alert('ERROR! ' + obj['message']);
            }else{
                alert('Unknown Error!');
            }
        });
    }
})();