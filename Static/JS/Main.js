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
        $('#Admin.Page').find('#saveGlobalSettings').on('click', saveGlobalSettings);
    }

    function saveGlobalSettings(event){
        var $row = $(event.target).closest('TR');

        var data = {};

        data['Command'] = 'SaveGlobals';
        data['StartingDisplayDate'] = $row.find('#StartingDisplayDate').val();
        data['Retail'] = $row.find('#Retail').val();
        data['SalesLimit'] = $row.find('#SalesLimit').val();
        data['DaysApart'] = $row.find('#DaysApart').val();
        data['Level1'] = $row.find('#Level1').val();
        data['Level2'] = $row.find('#Level2').val();
        data['Level3'] = $row.find('#Level3').val();

        console.log(data);

        $.post('/Admin', data, function(data){
            obj = $.parseJSON(data);
            if(obj['status'] == 'OK'){
                alert('Saved!');
            }else{
                alert('ERROR! ' + obj['message']);
            }
        });
    }
})();