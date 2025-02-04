/**
* 2018 Ecomdoo Software SRL
*
* @author Ecomdoo Software SRL (LOT.ai)
* @copyright 2018 Ecomdoo Software SRL
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0) 
*/

function showMatchResult(id_category, response_class, response_text) {
    
    $('#gcatselect_'+id_category).parent().append('<span class="match_notification '+response_class+'">'+response_text+'</span>');
    
    setTimeout(function(){$('#gcatselect_'+id_category).parent().find('.match_notification').remove()},2000);
    
}


function downloadGoogleTaxonomy() {
    
    url=ajax_link_match;
    
    $.ajax({
          type: "POST",
          url: url,
          headers: { "cache-control": "no-cache" },
          async: false,
          cache: false, 
          dataType : "text",
          data: 'function=downloadGoogleCategories&inputval=1',
          success: function(response)
                        {
                            
                            if (response != '') {
                                response = JSON.parse(response);
                                if ( (response.success == 1) && (response.result != '') ) {
                                    if (response.result !== true) alert(response.result);
                                } else {
                                    alert(response); 
                                } 
                                
                            } else {
                                   alert(response); 
                                }
                           
                            
                        },
        
        complete: function(){
            setTimeout(function(){
            location.reload();
            },1000);    
            
        },

         error: function(XMLHttpRequest, textStatus, errorThrown) {
           alert('Error: + '.errorThrown);
         }

        });
    
}


function getGoogleCatsList(inputval,idfield,id_category) {

    url=ajax_link_match;
    
    if (typeof $('#'+idfield).parent().find('ul.gsugg').html() != 'undefined')
        $('#'+idfield).parent().find('ul.gsugg').remove();
    
    if (typeof $('#'+idfield).parent().find('.loading').html() != 'undefined')
        $('#'+idfield).parent().find('.loading').remove();
    
    $('#'+idfield).parent().append('<div class="loading"></div>');
    
    $.ajax({
          type: "POST",
          url: url,
          headers: { "cache-control": "no-cache" },
          async: false,
          cache: false, 
          dataType : "text",
          data: 'function=getCatList&inputval='+inputval+'&idfield='+idfield+'&id_category='+id_category,
          success: function(response)
                        {
                            
                            if (response != '') {
                                response = JSON.parse(response);
                                if ( (response.success == 1) && (response.result != '') ) {
                                    $('#'+idfield).parent().append('<ul class="gsugg">'+response.result+'</ul>');
                                } else {
                                    $('#'+idfield).parent().append('<ul class="gsugg"><li class="notfound" value="0">'+not_g_cats_found+'</li></ul>'); 
                                } 
                                
                            } else {
                                    $('#'+idfield).parent().append('<ul class="gsugg"><li class="notfound" value="0">'+not_g_cats_found+'</li></ul>'); 
                                }
                           
                            
                        },
        complete: function(){
            setTimeout(function(){
            $('#'+idfield).parent().find('.loading').remove();
            $('#'+idfield).parent().find('ul.gsugg').show();
            },1000);    
            
        },
         error: function(XMLHttpRequest, textStatus, errorThrown) {
           alert('Error: + '.errorThrown);
         }

        });
    
    
}


function getGoogleMainCatsList(inputval,idfield) {

    url=ajax_link_match;
    console.log(url);
    
    if (typeof $('#'+idfield).parent().find('ul.gsugg').html() != 'undefined')
        $('#'+idfield).parent().find('ul.gsugg').remove();
    
    if (typeof $('#'+idfield).parent().find('.loading').html() != 'undefined')
        $('#'+idfield).parent().find('.loading').remove();
    
    $('#'+idfield).parent().append('<div class="loading"></div>');
    
    $.ajax({
          type: "POST",
          url: url,
          headers: { "cache-control": "no-cache" },
          async: false,
          cache: false, 
          dataType : "text",
          data: 'function=getMainCatList&inputval='+inputval+'&idfield='+idfield,
          success: function(response)
                        {
                            
                            if (response != '') {
                                response = JSON.parse(response);
                                if ( (response.success == 1) && (response.result != '') ) {
                                    $('#'+idfield).parent().append('<ul class="gsugg">'+response.result+'</ul>');
                                } else {
                                    $('#'+idfield).parent().append('<ul class="gsugg"><li class="notfound" value="0">'+not_g_cats_found+'</li></ul>'); 
                                } 
                                
                            } else {
                                    $('#'+idfield).parent().append('<ul class="gsugg"><li class="notfound" value="0">'+not_g_cats_found+'</li></ul>'); 
                                }
                           
                            
                        },
        complete: function(){
            setTimeout(function(){
            $('#'+idfield).parent().find('.loading').remove();
            $('#'+idfield).parent().find('ul.gsugg').show();
            },1000);    
            
        },
         error: function(XMLHttpRequest, textStatus, errorThrown) {
           alert('Error: + '.errorThrown);
         }

        });
    
    
}





function applyMatch(id_category,google_id) {

    url=ajax_link_match;
    
    $.ajax({
          type: "POST",
          url: url,
          headers: { "cache-control": "no-cache" },
          async: false,
          cache: false, 
          dataType : "text",
          data: 'function=applyMatch&google_id='+google_id+'&id_category='+id_category,
          success: function(response)
                        {
                            if (response != '') {
                                
                                //alert(response);
                                response = JSON.parse(response);
                                if ( (response.success == 1) && (response.result != '') ) {
                                    if (response.result.indexOf('error') > 0)
                                    showMatchResult(id_category, 'error', response.result.replace('error',''));    
                                    else     
                                    showMatchResult(id_category, 'succes', response.result);
                                    //alert(response.result);
                                } else {
                                    showMatchResult(id_category, 'error', error_google_matching);
                                    
                                }
                                
                            }
                           
                            
                        },
         error: function(XMLHttpRequest, textStatus, errorThrown) {
           alert('Error: + '.errorThrown);
         }

        });
    
    
}




$(document).ready(function() {
    
    if (typeof $('div.loading.download').html() != 'undefined') {
        
         setTimeout(function(){
            downloadGoogleTaxonomy();
            },2000);
        
    }
    
    
    $('#google_category_main').click(function(){ 
        
        getGoogleMainCatsList($(this).val(),$(this).attr('id'));
        
    });
    
    $('#google_category_main').keyup(function(){ 
        
        getGoogleMainCatsList($(this).val(),$(this).attr('id'));
        
    });
    
    $('.gcatselect').click(function(){ 
        
        getGoogleCatsList($(this).val(),$(this).attr('id'),$(this).attr('data-id_category'));
        
    });
    
    $('.gcatselect').keyup(function(){ 
        
        getGoogleCatsList($(this).val(),$(this).attr('id'),$(this).attr('data-id_category'));
        
    });
    
    
    $('#configuration_form').on('click', '.gsugg li', function(){ 
    
        if (!$(this).hasClass('notfound')) {
        $('#google_id').val($(this).attr('value'));
        $('#google_category_main').val($(this).text());
        }
        $(this).parent().remove();
        
    });
    
    $('#configuration_form').on('mouseleave', '.gsugg', function(){
        
        $(this).remove();
        $('#google_category_main').val('');
        
    });
    
    $('#table-category td').on('click', '.gsugg li', function(){ 
    
        //console.log($(this).attr('value') + ' - ' + $(this).text());
        if (!$(this).hasClass('notfound')) {
        $(this).parent().parent().find('.gcatselect').attr('data-google_id',$(this).attr('value'));
        $(this).parent().parent().find('.gcatselect').val($(this).text());
        $(this).parent().parent().find('.gcatselect').attr('title',$(this).text());
        }
        $(this).parent().remove();
        
    });
    
    $('#table-category td').on('mouseleave', '.gsugg', function(){
        
        $(this).remove();
        
    });
    
    
    $('.apply_gcat').mouseover(function(){
        $('ul.gsugg').remove(); 
    });
    
    
    $('.apply_gcat').click(function(apev) {
        
        apev.preventDefault();
        
        if ($('#gcatselect_'+$(this).attr('data-id_category')).val() == '')
        applyMatch($(this).attr('data-id_category'),0);    
        else    
        applyMatch($(this).attr('data-id_category'),$('#gcatselect_'+$(this).attr('data-id_category')).attr('data-google_id'));
        
        
    });
    
    
    $('button.back').click(function(){
        window.history.back();
    });
    
    
    $('#table-category td.text-right a.btn').each(function(){
        
        tr_id_cat = $(this).closest('tr').attr('id').replace('tr__','');
        tr_id_cat = tr_id_cat.replace('_0','');
        
        if ($('#haschildren_'+tr_id_cat).val() == 0)
        $(this).hide();    
        else
        $(this).text(view_button);
        
    });
    

});
