/* for front */


function putHeaderAlertDesign() {

            var html_header_alert =`<div id="ecd_headeralert">
                                <div class="container">
                                   `+alert_content+`
                                </div>
                            </div>`;


            $('body').prepend( html_header_alert );
            $('body > main').css('margin-top', $('#ecd_headeralert').css('height'));
    
}


$(document).ready(function(){

     setTimeout(function(){ putHeaderAlertDesign(); },500);
 
});



