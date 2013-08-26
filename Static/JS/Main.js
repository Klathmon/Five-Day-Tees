/**
 * Created By: Gregory Benner
 * Date: 8/25/13
 * Time: 7:36 PM
 */
(function(){
    /**
     * DOM Loaded
     */
    $(function(){
        //Attach All Handlers
        attachHandlers();
    });

    function attachHandlers(){
        $('#Admin.Page')
            .find('#SaveGlobalSettings').on('click', saveGlobalSettings)
            .find('#SaveItem').on('click', saveItem)
            .find('#DeleteItem').on('click', deleteItem);
    }

    /** Admin Page Functions **/

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


    /** Helper Functions **/

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