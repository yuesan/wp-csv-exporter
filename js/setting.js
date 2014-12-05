jQuery(function($) {
    $('#wce-lsubmit').on('click', function() {
        var license_key = $('#wce-license-key').val();
        $.ajax({
            type: 'POST',
            url: 'http://services.flipclap.co.jp/wp-csv-exporter/license-key.php',
            data: {
                "license_key": license_key
            },
            success: function(data) {
                if(data == 'success'){

                }                
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log( textStatus );
            }
        });
    });
});