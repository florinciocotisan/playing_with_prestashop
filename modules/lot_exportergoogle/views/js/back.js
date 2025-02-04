/**
* 2018 Ecomdoo Software SRL
*
* @author Ecomdoo Software SRL (LOT.ai)
* @copyright 2018 Ecomdoo Software SRL
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0) 
*/



$(document).ready(function() {
    

 
if ($('#feedid').val() > 0) {
    editfeed($('#feedid').val());
    $([document.documentElement, document.body]).animate({
        scrollTop: $("#cronlink").offset().top
    }, 2000);
} 
    
if ($('#feedtype').val() == 'xml') {
    $('#feedheader').html('<products>'); 
    $('#feedfooter').html('</products>');
}    
    
$('#feedtype').on('change',function() {
    if ($('#feedtype').val() == 'xml')  { $('#feedcontentdiv').show(); $('#csvfeedcontentdiv').hide(); $('#txtfeedcontentdiv').hide(); $('.sodseparator').hide(); $('.xml').show(); $('.csv').hide(); $('#feedheader').html('<products>'); $('#feedfooter').html('</products>');  }
    if ($('#feedtype').val() == 'csv')  { $('#feedcontentdiv').hide(); $('#csvfeedcontentdiv').show(); $('#txtfeedcontentdiv').hide(); $('.sodseparator').show(); $('.xml').hide(); $('.csv').show(); $('.txt').hide(); $('#feedheader').html(''); $('#feedfooter').html(''); }
    if ($('#feedtype').val() == 'txt')  { $('#feedcontentdiv').hide(); $('#csvfeedcontentdiv').hide(); $('#txtfeedcontentdiv').show(); $('.sodseparator').show(); $('.xml').show(); $('.csv').hide(); $('.txt').show(); $('#feedheader').html(''); $('#feedfooter').html(''); }
});
    
$('#closeprogress').click(function() {
    $('#lotmyProgress').hide();
    endurl=location.href.indexOf('&editfeed');
    location.href=location.href.substring(0,endurl);
});    
    
$('.addelement').click(function() {
$('#contenttexteditor').fadeIn(); 
$('#structureid').val($(this).parent().attr('id'));    
});  
    
    
$('.cancel').click(function() {
$(this).parent().parent().fadeOut();
if ($('#addvaluetexteditor').css('z-index') == '9992')   
$('.variableconfigurator_bg').hide(); 
$('#addvaluetexteditor').css('z-index','9992');    
}); 
    
 
 $('#csvfeedcontentdiv #btn_right').click(function(){
   if ( Math.round(parseInt($('#captabeldiv').css('left')) -  $('#csvfeedcontentdiv').width() * 0.7) > Math.round(0 - 4400 * 0.7)  ) 
    $('#captabeldiv').css('left',Math.round(parseInt($('#captabeldiv').css('left')) -  $('#csvfeedcontentdiv').width() * 0.7)+'px');
else $('#captabeldiv').css('left', Math.round(0 - 4400 + $('#csvfeedcontentdiv').width()) + 'px' );     
});

$('#csvfeedcontentdiv #btn_left').click(function(){
    if (Math.round(parseInt($('#captabeldiv').css('left')) +  $('#csvfeedcontentdiv').width() * 0.7) < 0)
    $('#captabeldiv').css('left',Math.round(parseInt($('#captabeldiv').css('left')) +  $('#csvfeedcontentdiv').width() * 0.7)+'px');
    else $('#captabeldiv').css('left','0px');
});   
    

    
    
});


function addelementxml(ob) {
$('#contenttexteditor').fadeIn();    
$('#structureid').val($(ob).parent().attr('id'));     
}




function removelelem(ob) {
oldid=$(ob).parent().attr('id');    
  $(ob).parent().html('');    
$('#'+oldid).attr('id','deleted_'+oldid);    
}

function editelem(ob) {    
$('#editelemeditor').fadeIn();    
$('#editlabel').val($(ob).parent().attr('id'));
$('#newlabel').val($(ob).parent().attr('id').substr(0,$(ob).parent().attr('id').indexOf('_u-')));   
}


function removevalue(ob) {
valuetoeliminate='<div class="xmlvalue"><span class="spanvaloare">'+$('#'+$(ob).parent().parent().attr('id')+' .xmlvalue .spanvaloare').html()+'</span><span class="removeelem" onclick="removevalue(this)"><img src="../img/admin/disabled.gif"></span><span class="editelem" onclick="editvaluetxt(this)"><img src="../img/admin/edit.gif"></span></div>'; 
  $(ob).parent().parent().html($(ob).parent().parent().html().replace(valuetoeliminate,''));       
}



function editfeed(idfeed) {
    

    
    $('#feedname').val($('#editfeedname_'+idfeed).val());
     $('#feedid').val(idfeed);
    if ($('#editfeedtype_'+idfeed).val() == 'xml') {
     $('#feedtype').val('xml'); 
    $('#feedcontentdiv').show(); $('#csvfeedcontentdiv').hide(); $('#txtfeedcontentdiv').hide(); $('.sodseparator').hide(); $('.xml').show(); $('.csv').hide();   
        
        
     editheader=$('#editheader_'+idfeed).html().replace(/&lt;/g,'<');    
     editheader=editheader.replace(/&gt;/g,'>');
     $('#feedheader').val(editheader);
        
     editfooter=$('#editfooter_'+idfeed).html().replace(/&lt;/g,'<');        
     editfooter=editfooter.replace(/&gt;/g,'>');        
        
     $('#feedfooter').val(editfooter);   
        
         
     $('#structure_1 > .subinner').html($('#editcode_'+idfeed).text());   
     
    }
    
    
    if ($('#editfeedtype_'+idfeed).val() == 'csv') {
     $('#feedtype').val('csv');
     $('#feedcontentdiv').hide(); $('#csvfeedcontentdiv').show(); $('#txtfeedcontentdiv').hide(); $('.sodseparator').show(); $('.xml').hide(); $('.csv').show(); $('.txt').hide(); $('#feedheader').html(''); $('#feedfooter').html('');
        
    
     $('#separatorvalmultime').val($('#editvalueseparator_'+idfeed).val());
        
     $('#separatorelemente').val($('#editfielseparator_'+idfeed).val());  
        
    var separatorelement=$('#separatorelemente').children();
        
    for (i=0;i<=separatorelement.length;i++) {
       if ($(separatorelement[i]).attr('value') == $('#editfielseparator_'+idfeed).val()) $(separatorelement[i]).attr('selected','selected');  
    } 
        
        
    separatorvalmultime=$('#separatorvalmultime').children();
    for (i=0;i<=separatorvalmultime.length;i++) {
       if ($(separatorvalmultime[i]).attr('value') == $('#editvalueseparator_'+idfeed).val()) $(separatorvalmultime[i]).attr('selected','selected');  
    }     
        
        
    editcapuritabel=$('#editheader_'+idfeed).html().split($('#editfielseparator_'+idfeed).val());
        
    formatcsvcode=$('#csvcode_'+idfeed).html().replace(/&lt;/g,'<');   
    formatcsvcode=formatcsvcode.replace(/&gt;/g,'>'); 
        
    editcsvvalori=formatcsvcode.split($('#editfielseparator_'+idfeed).val());   
        
    for (i=1;i<=editcapuritabel.length;i++) 
        $('#cap_'+i).val(editcapuritabel[i-1].replace(/"/g,''));
    for (i=1;i<=editcsvvalori.length;i++)  
        $('#colvalue_'+i).val(editcsvvalori[i-1].replace(/"/g,''));
    
    
        
    }
    
    
    
    
    if ($('#editfeedtype_'+idfeed).val() == 'txt') {
     $('#feedtype').val('txt'); 
    $('#feedcontentdiv').hide(); $('#csvfeedcontentdiv').hide(); $('#txtfeedcontentdiv').show(); $('.sodseparator').show(); $('.xml').show(); $('.csv').hide(); $('.txt').show(); 
        
        
     editheader=$('#editheader_'+idfeed).html().replace(/&lt;/g,'<');    
     editheader=editheader.replace(/&gt;/g,'>');
     $('#feedheader').val(editheader);
        
     editfooter=$('#editfooter_'+idfeed).html().replace(/&lt;/g,'<');        
     editfooter=editfooter.replace(/&gt;/g,'>');        
        
     $('#feedfooter').val(editfooter);   
        
     $('#txtfeedcontentdiv > #feed').html($('#editcode_'+idfeed).text());
        
      $('#separatorvalmultime').val($('#editvalueseparator_'+idfeed).val());
        
     $('#separatorelemente').val($('#editfielseparator_'+idfeed).val());  
        
    var separatorelement=$('#separatorelemente').children();
        
    for (i=0;i<=separatorelement.length;i++) {
       if ($(separatorelement[i]).attr('value') == $('#editfielseparator_'+idfeed).val()) $(separatorelement[i]).attr('selected','selected');  
    } 
        
        
    separatorvalmultime=$('#separatorvalmultime').children();
    for (i=0;i<=separatorvalmultime.length;i++) {
       if ($(separatorvalmultime[i]).attr('value') == $('#editvalueseparator_'+idfeed).val()) $(separatorvalmultime[i]).attr('selected','selected');  
    }       
        
      
    }




}



function editvaluetxt(ob) {
    $('.variableconfigurator_bg').show();
     $('.campgolvaloare').hide();
    
    $('#addvaluetexteditor').fadeIn();
     if ($('#feedtype').val() == 'xml') {
        $('#structureidvaluetxt').val($(ob).parent().parent().attr('id'));
        fulltext=$('#'+$(ob).parent().parent().attr('id')+' > .xmlvalue .spanvaloare').html(); 
     }
    else if ($('#feedtype').val() == 'csv') {
      fulltext=$('#'+$(ob).attr('id').replace('col_','colvalue_')).val(); 
    }
    else  {   
    $('#structureidvaluetxt').val($(ob).parent().attr('id'));
    fulltext=$('#'+$(ob).parent().attr('id')+' .spanvaloare').html();
    }
    
    
    textinainte=fulltext.substring(0,fulltext.indexOf('{')-1);
    textdupa=fulltext.substring(fulltext.indexOf('}')+2);
    variabiladepus=fulltext.substring(fulltext.indexOf('{')+1,fulltext.indexOf('}'));
    
    if (variabiladepus.trim() == '') { textinainte=fulltext; textdupa=''; }  
    
    variabiladepus=variabiladepus.replace(/\&gt;/g,'>');    
    variabiladepus=variabiladepus.replace(/\&lt;/g,'<'); 
    textinainte=textinainte.replace(/\&gt;/g,'>');    
    textinainte=textinainte.replace(/\&lt;/g,'<'); 
    textdupa=textdupa.replace(/\&gt;/g,'>');    
    textdupa=textdupa.replace(/\&lt;/g,'<');
    
    $('#variabilatext').val(variabiladepus);
    $('#variabilatext_before').val(textinainte);
    $('#variabilatext_after').val(textdupa);
    
    
    $('#variabilatext_2').val('');
    $('#variabilatext_3').val('');
    $('#additionalele_2').hide();
    $('#additionalele_3').hide();
    $('.textshow').show();
    $('.texthide').hide();
    
    
}

function removevaluetxt(ob) {
  $(ob).parent().html(''); 
}

function posteditelem() {    
    
$('.campgol').hide();    

if ($('#newlabel').val().trim() != '') {        
    
oldlabel=$('#editlabel').val().substr(0,$('#editlabel').val().indexOf('_u-'));    
newidlabel=$('#newlabel').val()+'_u-'+Math.floor(Math.random() * 100000);
    
$('#'+$('#editlabel').val()+' > .xmlelement').html($('#'+$('#editlabel').val()+' > .xmlelement').html().replace(oldlabel,$('#newlabel').val()));
    
$('#'+$('#editlabel').val()+' > .endxmlelement').html($('#'+$('#editlabel').val()+' > .endxmlelement').html().replace(oldlabel,$('#newlabel').val()));    
    
 $('#'+$('#editlabel').val()).attr('id',newidlabel); 
$('#editelemeditor').fadeOut();
    
}
else {
 $('.campgol').show();       
}    
    
}


function insertlabelxml() {

$('.campgol').hide();    

if ($('#valoarelabel').val().trim() != '') {    
    
newidlabel=$('#valoarelabel').val()+'_u-'+Math.floor(Math.random() * 100000);
    
    /*continut='<div class="innerelements" id="'+newidlabel+'"><span class="xmlelement">'+'&lt;'+$('#valoarelabel').val()+'&gt;'+'</span><span class="removeelem" onclick="removelelem(this)"><img src="../img/admin/disabled.gif" /></span><span class="editelem" onclick="editelem(this)"><img src="../img/admin/edit.gif" /></span>'; */
    
     continut='<div class="innerelements" id="'+newidlabel+'"><span class="xmlelement">'+$('#valoarelabel').val()+'</span><span class="removeelem" onclick="removelelem(this)"><img src="../img/admin/disabled.gif" /></span><span class="editelem" onclick="editelem(this)"><img src="../img/admin/edit.gif" /></span>'; 
    
continut=continut+'<button class="addvalue button btn exclusive" onclick="addvaluetexteditor(this)">'+$('#mainaddvalue').html()+'</button>';      

continut=continut+'<button class="addelement button btn exclusive" onclick="addelementxml(this)">'+$('#mainaddelement').html()+'</button>';  
/*    
continut=continut+'<div class="subinner"></div><span class="endxmlelement">'+'&lt;/'+$('#valoarelabel').val()+'&gt;'+'</span>';
    */
continut=continut+'<div class="subinner"></div><span class="endxmlelement">'+$('#valoarelabel').val()+'</span>';    

$('#'+$('#structureid').val()+' .subinner').html($('#'+$('#structureid').val()+' .subinner').html() + continut);   
    
$('#contenttexteditor').fadeOut();
}
else {
    $('.campgol').show();
}    
    
}


/*
function insertvaluexml() {
    
if ( $('#'+$('#structureidvalue').val() + ' .xmlvalue').html() && ($('#'+$('#structureidvalue').val() + ' .xmlvalue').html() != '') ) {
$('#'+$('#structureidvalue').val() + ' .xmlvalue .spanvaloare').html($('#variabila').val()); }
else {    
$('#'+$('#structureidvalue').val()).html($('#'+$('#structureidvalue').val()).html().replace('<div class="subinner"','<div class="xmlvalue"><span class="spanvaloare">'+$('#variabila').val()+'</span><span class="removeelem" onclick="removevalue(this)"><img src="../img/admin/disabled.gif" /></span><span class="editelem" onclick="editvalue(this)"><img src="../img/admin/edit.gif" /></span></div><div class="subinner"'));
}
    
$('#addvalueeditor').fadeOut();

}
*/

function addvaluetexteditor(sp=0) {
    
    $('.variableconfigurator_bg').show();
    $('.campgolvaloare').hide();
    
    if (sp != 0) { 
        
    /* for xml */    
    if ($('#feedtype').val() == 'xml') {    
       $('#structureidvaluetxt').val($(sp).parent().attr('id'));
   
        if ( $('#'+$('#structureidvaluetxt').val() + ' > .xmlvalue').html() && ($('#'+$('#structureidvaluetxt').val() + ' > .xmlvalue').html() != '') ) 
        $('#'+$('#structureidvaluetxt').val() + ' > .xmlvalue .editelem').click();
        else {
          $('#addvaluetexteditor').fadeIn();
    $('#variabilatext').val('');
    $('#variabilatext_before').val('');
    $('#variabilatext_after').val('');
    $('#variabilatext_2').val('');
    $('#variabilatext_3').val('');
    $('#additionalele_2').hide();
    $('#additionalele_3').hide();
    $('.textshow').show();
    $('.texthide').hide();  
        }
        
    }
        
    /* for csv */    
    if ($('#feedtype').val() == 'csv') {    
       $('#structureidvaluetxt').val($(sp).attr('id').replace('col_','colvalue_')); 
       if   ($('#'+$('#structureidvaluetxt').val()).val() != '')
           editvaluetxt(sp);
        else {
           $('#addvaluetexteditor').fadeIn();
    $('#variabilatext').val('');
    $('#variabilatext_before').val('');
    $('#variabilatext_after').val('');
    $('#variabilatext_2').val('');
    $('#variabilatext_3').val('');
    $('#additionalele_2').hide();
    $('#additionalele_3').hide();
    $('.textshow').show();
    $('.texthide').hide();   
        }
    }
        
        
    }
      else { 
          
    $('#structureidvaluetxt').val('');
        
    $('#addvaluetexteditor').fadeIn();
    $('#variabilatext').val('');
    $('#variabilatext_before').val('');
    $('#variabilatext_after').val('');
    $('#variabilatext_2').val('');
    $('#variabilatext_3').val('');
    $('#additionalele_2').hide();
    $('#additionalele_3').hide();
    $('.textshow').show();
    $('.texthide').hide();
    }
        
}


function insertvaluexmltxt() {
    
    valoarecombinata=$('#variabilatext').val();
    
    if ($('#variabilatext_2').val().trim() != '')
    valoarecombinata+=' AND '+$('#variabilatext_2').val();
    
    if ($('#variabilatext_3').val().trim() != '')
    valoarecombinata+=' AND '+$('#variabilatext_3').val();
    
    if (valoarecombinata.trim() != '') 
      valoarecombinata='{'+valoarecombinata+'}';
    
    if ($('#variabilatext_before').val().trim() != '')
    valoarecombinata=$('#variabilatext_before').val() + ' ' + valoarecombinata;
    
    if ($('#variabilatext_after').val().trim() != '')
    valoarecombinata=valoarecombinata + ' ' + $('#variabilatext_after').val();
    
    if (valoarecombinata.trim() == '') $('.campgolvaloare').show();
    else {
    
    if ($('#feedtype').val() == 'xml') {
        
    if ( $('#'+$('#structureidvaluetxt').val() + ' > .xmlvalue').html() && ($('#'+$('#structureidvaluetxt').val() + ' > .xmlvalue').html() != '') )
$('#'+$('#structureidvaluetxt').val() + ' > .xmlvalue .spanvaloare').html(valoarecombinata);    
    else    $('#'+$('#structureidvaluetxt').val()).html($('#'+$('#structureidvaluetxt').val()).html().replace('<div class="subinner"','<div class="xmlvalue"><span class="spanvaloare">'+valoarecombinata+'</span><span class="removeelem" onclick="removevalue(this)"><img src="../img/admin/disabled.gif" /></span><span class="editelem" onclick="editvaluetxt(this)"><img src="../img/admin/edit.gif" /></span></div><div class="subinner"'));
        
    }
    
    else if ($('#feedtype').val() == 'csv') {
    $('#'+$('#structureidvaluetxt').val()).val(valoarecombinata);
    }
    
    else {
        
    if ($('#structureidvaluetxt').val() != '') {
        $('#'+$('#structureidvaluetxt').val()+' .spanvaloare').html(valoarecombinata);
    } 
    
        
    else {
        idelementnou='var_'+Math.floor(Math.random()*10000);
        nouelement='<div class="txtelem" id="'+idelementnou+'"><span class="spanvaloare">'+valoarecombinata+'</span><span class="removeelem" onclick="removevaluetxt(this)"><img src="../img/admin/disabled.gif" /></span><span class="editelem" onclick="editvaluetxt(this)"><img src="../img/admin/edit.gif" /></span></div>';
        $('#txtfeedcontentdiv #feed').html($('#txtfeedcontentdiv #feed').html() + nouelement);
    }
        
    }
     $('#addvaluetexteditor').fadeOut();
    $('.variableconfigurator_bg').hide();
        
    }
}


function showformula() {
    $('#formula').show();
}

function showcondition() {
    if ($('#condition_2').css('display') == 'block') {
        $('#condition_3').show();
        $('#condlabel_3').html($('#selectedvalue').val()); 
        $('#conditiondiv button').hide();
    } else if ($('#condition').css('display') == 'block') {
        $('#condition_2').show();
        $('#condlabel_2').html($('#selectedvalue').val()); 
    }
    else {
    $('#condition').show();
    $('#condlabel').html($('#selectedvalue').val());
    }
}

function removecond(nrid) {
    
    if (nrid == 1) {
        $('#conditionvalue').val('');
        $('#condoperator').val('');
        $('#displayvalue').val('');
        $('#condition').hide();
    } else {
       $('#conditionvalue_'+nrid).val('');
        $('#condoperator_'+nrid).val('');
        $('#displayvalue_'+nrid).val('');
        $('#condition_'+nrid).hide(); 
    }
    
    $('#conditiondiv button').show();
    
}


function selectvar(valoare) {
    $('#positionvalue').hide();
    $('#selectedvalue').attr('readonly','readonly');
    $('#htmlvar').val(0);
    $('#htmlnote').hide();
     $('#formuladiv').show();
    $('#conditiondiv').show();
    $('#positionvalue').val('');
    $('#selectedvalue').css('width','100%');
    if (valoare == 'html') { valoare='';
    $('#htmlvar').val(1);                       
    $('#selectedvalue').removeAttr('readonly');                        
    $('#formuladiv').hide();
    $('#conditiondiv').hide();
    $('#htmlnote').show();                        
                           }
    $('#selectedvalue').val(valoare);
    if (valoare.indexOf('?position') > 0) { 
            if ($('#combsep').attr('checked')) {
             if (valoare.indexOf('ombination') > 0) 
                 $('#positionvalue').val('position');
            else  {
               $('#positionvalue').show();
            $('#selectedvalue').css('width','calc(100% - 70px)');  
            }    
            } else {
            $('#positionvalue').show();
            $('#selectedvalue').css('width','calc(100% - 70px)');
            }
                                          }
}


function addadditionalvar(nrelem) {
    if ($('#additionalele_'+nrelem).css('display') == 'none') {
    $('#additionalele_'+nrelem).show();
    $('.elem_'+nrelem+' .texthide').show();
    $('.elem_'+nrelem+' .textshow').hide();    
    } else {
       $('#additionalele_'+nrelem).hide();
        $('.elem_'+nrelem+' .textshow').show(); 
        $('.elem_'+nrelem+' .texthide').hide();
        $('#variabilatext_'+nrelem).val('');
    }
}

function configurevariable(saveid) {
    
    $('.variableconfigurator').show();
    $('#addvaluetexteditor').css('z-index','9');
    //$('.variableconfigurator_bg').show();
    $('#positionvalue').val('');
    $('#positionvalue').hide();
    
    /*reset values */
        $('#selectedvalue').val('');
        $('#conditionvalue').val('');
        $('#condoperator').val('');
        $('#displayvalue').val('');
        $('#condition').hide();
        $('#conditionvalue_2').val('');
        $('#condoperator_2').val('');
        $('#displayvalue_2').val('');
        $('#condition_2').hide();
        $('#conditionvalue_3').val('');
        $('#condoperator_3').val('');
        $('#displayvalue_3').val('');
        $('#condition_3').hide();
        $('#formula').hide();
        $('#operator').val('');
        $('#operatorvalue').val('');
    /*end reset values */
    if ($('#combsep').attr('checked'))
    sodcomb=1;
    else sodcomb=0;

    
    $('#putelementin').val(saveid);
    variabila='';
    
    $.ajax({
  type: "GET",
  url: ajax_link,
  headers: { "cache-control": "no-cache" },
  async: false,
  cache: false, 
  dataType : "text",
  data: 'getvariable=1&comb='+sodcomb+'&secure_key='+$('#sodcheiesig').val(),
  success: function(response)
				{
                    
                   if (response != 1) {
                   $('.variableselector').html(response);
                   }
                   
                  
                },
 error: function(XMLHttpRequest, textStatus, errorThrown) {
   alert('Error: + '.errorThrown);
 }
  
}); 
    
    
}


function insertvariable() {
    
    $('#variableconfigurator .error').hide();
    
    formatvariabila='';
    if ($('#htmlvar').val() == 0) {
    variabila=$('#selectedvalue').val();
    if ($('#positionvalue').val() != '') variabila=variabila+$('#positionvalue').val();
    
    if (($('#operator').val() != '') && $('#operatorvalue').val() != '') {
     variabila='CALC('+variabila+' '+$('#operator').val()+' '+$('#operatorvalue').val()+')'; }
    
    if ( ($('#condoperator').val() != '') && ($('#conditionvalue').val() != '') && ($('#displayvalue').val() != '') ) {
      formatvariabila=' IF('+variabila+' '+$('#condoperator').val()+' '+$('#conditionvalue').val()+')'+$('#displayvalue').val();  
    }
    
    if ( ($('#condoperator_2').val() != '') && ($('#conditionvalue_2').val() != '') && ($('#displayvalue_2').val() != '') ) {
      formatvariabila+=' IF('+variabila+' '+$('#condoperator_2').val()+' '+$('#conditionvalue_2').val()+')'+$('#displayvalue_2').val();  
    }
    
    if ( ($('#condoperator_3').val() != '') && ($('#conditionvalue_3').val() != '') && ($('#displayvalue_3').val() != '') ) {
      formatvariabila+=' IF('+variabila+' '+$('#condoperator_3').val()+' '+$('#conditionvalue_3').val()+')'+$('#displayvalue_3').val();  
    }
    
    if (formatvariabila == '') formatvariabila=variabila;
    }
    else { 
        cleanvalue=stripHTML($('#selectedvalue').val());
        formatvariabila='TEXT('+cleanvalue+')'; }
    
    if (formatvariabila == '') $('.variableconfigurator .error').show();
    else {
        $('#'+$('#putelementin').val()).val(formatvariabila);
        $('.variableconfigurator').fadeOut();
        $('#addvaluetexteditor').css('z-index','9992');
        
    }

}



function savefeed(exportasfile) {
       
    
//alert($('#combsep').is(":checked"));    
if ($('#onlyactive').is(":checked") === true)    
onlyactive=1;
else onlyactive=0;
    
if ($('#combsep').is(":checked") === true)    
combsep=1;
else combsep=0;    

var restrictions={
    'only_active' : onlyactive,
    'combsep' : combsep,
    'filter_category' : $('#filter_category').val(),
    'filter_manufacturer' : $('#filter_manufacturer').val(),
    'filter_feature' : $('#filter_feature').val(),
    'filter_attribute' : $('#filter_attribute').val() 
};  
    

restrictions=JSON.stringify(restrictions);
    
   
var feedsettings = {
    'idprefix' : $('#idprefix').val(),
    'currency' : $('#currency').val(),
    'image_formats' : $('#image_formats').val(), 
    'id_lang' : $('#limba').val() 
}    
    
feedsettings = JSON.stringify(feedsettings); 
    
$('#feedsavedok').hide();
$('#feedsavederror').hide();    
 
url= ajax_link;
valueseparator='';    
fieldseparator='';    
feeddata='';
capuritabel=''; 
feedheader='';
feedfooter='';
feeddatahtml='';
    
feeddata = '';    
    
feedname=$('#feedname').val();
feedid=$('#feedid').val();  

if (feedname == '') alert(name_error);    
else {
    
//put waiting icon
//alert(url);
    
    
$.ajax({
  type: "POST",
  url: url,
  headers: { "cache-control": "no-cache" },
  async: false,
  cache: false, 
  dataType : "text",
  data: 'savefeed=1&feedcontent='+feeddata+'&feeddatahtml='+feeddatahtml+'&capuritabel='+capuritabel+'&fieldseparator='+fieldseparator+'&valueseparator='+valueseparator+'&feedheader='+feedheader+'&feedfooter='+feedfooter+'&feedname='+feedname+'&feedid='+feedid+'&type='+$('#feedtype').val()+'&restrictions='+restrictions+'&feedsettings='+feedsettings,
  success: function(response)
				{
                   
                    if (response > 0) { 
                        
                    $('#feedsavedok').show();
                    $('#submitfeedformat').parent().find('.waiting').show();         setTimeout(function(){ initializefeed(response,0,exportasfile); },1000);           
                    
                                           
                    }
                    else $('#feedsavederror').show();
                    
                     
                  
                },
 error: function(XMLHttpRequest, textStatus, errorThrown) {
   alert('Error: + '.errorThrown);
 }
  
});
  
}
    
    
}
       
       
function putrestrictions(idfeed,cheiesec) {
    
url=ajax_link;    
    
    

restrictionshtml=escape($('#filterproducts .col-xs-12').html());    
    
restrictionshtml=restrictionshtml.replace(/\&gt;/g,'>');   
restrictionshtml=restrictionshtml.replace(/\&lt;/g,'<'); 
    

$.ajax({
  type: "POST",
  url: url,
  headers: { "cache-control": "no-cache" },
  async: false,
  cache: false, 
  dataType : "text",
  data: 'putrestrictions=1&feedrestrictionshtml='+restrictionshtml+'&updatedfeed='+idfeed,
  success: function(response)
				{
                    
                    //alert(response);                     
                  
                },
 error: function(XMLHttpRequest, textStatus, errorThrown) {
   alert('Error: + '.errorThrown);
 }
  
});    
    

}        






function deletefeed(idfeed) {
    
$('#deleteddok').hide();
$('#deletederror').hide();    
    
url=ajax_link;   
    
$.ajax({
  type: "POST",
  url: url,
  headers: { "cache-control": "no-cache" },
  async: false,
  cache: false, 
  dataType : "text",
  data: 'deletefeed='+idfeed,
  success: function(response)
				{
                    //alert(response);
                        if (response == 1) $('#deleteddok').show();
                    else $('#deletederror').show();
                    
                    setTimeout(function(){ location.reload(); }, 2000);
                     
                  
                },
 error: function(XMLHttpRequest, textStatus, errorThrown) {
   alert('Error: + '.errorThrown);
 }
  
});
    
}


function stripHTML(htmlinput) {
    var tempdom=document.createElement('div');
    tempdom.innerHTML=htmlinput;
    return tempdom.textContent || tempdom.innerText || "";
    
}



function progressbar(progres,progressnumber,total) {
   
     $('#lotmyBar').css('width',progres + '%');
     //$('#lotmyBarNumbers').html(progressnumber + ' / ' + total + ' ' + itemslang);    
   
    
}


function progressbaremptyfeed() {
    $('#lotmyProgress').addClass('emptyfeed');
}


function initializefeed(idfeed,procentprocessed,exportasfile) {
    
   url = ajax2_link;

    
    
if ($('#combsep').is(":checked") === true)    
combsep=1;
else combsep=0;    
   
  $('#lotmyProgress').show();

    
if (procentprocessed < 100)  {    
 
   
$.ajax({
  type: "GET",
  url: url,
  headers: { "cache-control": "no-cache" },
  async: false,
  cache: false, 
  dataType : "text",
  data: 'initialize_feed='+idfeed+'&combsep='+combsep+'&secure_key='+$('#sodcheiesig').val(),
  success: function(response)
				{
                    //alert(response);
                       
                   if (response != 1) {
                    response = JSON.parse(response);   
                     parti=response.split('_');
                     procentprocessed=Math.round(parti[1]*100/parti[0]);
                       
                    $('#submitfeedformat').parent().find('.waiting').hide();   
                     
                    if (Math.round(parti[1]*100/parti[0]) <= 100) {  
                    progressbar(Math.round(parti[1]*100/parti[0]),parti[1],parti[0]);
                    setTimeout(function() { initializefeed(idfeed,procentprocessed,exportasfile); },5);  
                     }
                       
                   } else progressbaremptyfeed(); 
                   
                  
                },
 error: function(XMLHttpRequest, textStatus, errorThrown) {
   alert('Error: + '.errorThrown);
 }
  
}); 
  

 
}

else  {
$('#lotmyProgress .inprocess').hide();
$('#lotmyProgress .finished').show();
$('#viewfeed').show();    
    
if (exportasfile == 0) {    
$('#viewfeed a').attr('href',feed_link+'&id_feed='+idfeed);
}
else {
$('#viewfeed a').hide();   
top.location.href = download_link+'&id_feed='+idfeed;
}
    
}
    
}



function canceledit() {
    endurl=location.href.indexOf('&editfeed');
    location.href=location.href.substring(0,endurl);
}


function getdirectlink(id_feed) {

    url=getdirectlink_link;
    
    $('tr.alertrow').hide();
    
    $.ajax({
          type: "POST",
          url: url,
          headers: { "cache-control": "no-cache" },
          async: false,
          cache: false, 
          dataType : "text",
          data: 'id_feed='+id_feed,
          success: function(response)
                        {
                            if (response != '') {
                                
                                response = JSON.parse(response);
                                
                                $('tr#alert_feed-'+id_feed+' input').val(response);
                                $('tr#alert_feed-'+id_feed).fadeIn();
                                
                            }
                           
                            
                        },
         error: function(XMLHttpRequest, textStatus, errorThrown) {
           alert('Error: + '.errorThrown);
         }

        });
    
    
}
