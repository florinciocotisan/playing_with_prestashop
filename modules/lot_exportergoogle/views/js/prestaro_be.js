/**
* 2018 Ecomdoo Software SRL
*
* @author Ecomdoo Software SRL (LOT.ai)
* @copyright 2020 Ecomdoo Software SRL
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0) 
*/

$(document).ready(function(){

$('.category_tree.multiple option').click(function(opc){
    
    if (opc.offsetX < 21) {
        
        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
        } else {
            $(this).addClass('selected');
        }
        
        refresh_selectionbox('.category_tree.multiple', '.multiple_selection_box');
        
    }
    else {
       
        if ($(this).hasClass('opened')) {
            $(this).removeClass('opened');
            $('.children-'+$(this).val()).slideUp();
        }
        else {
            $('.children-'+$(this).val()).slideDown();
            $(this).addClass('opened');    
        }
        
    }
    
    
});
    
  
function refresh_selectionbox(cattree,selection_box) {
    
    $(selection_box).html('');
    
    selectii = '';
    selectiivals = [];
    
    $(cattree + ' option.selected').each(function(){
        
        selectii = selectii + '<button item_id="'+$(this).val()+'">'+$(this).text()+'<span class="remove">x</span></button>';
        
        selectiivals.push($(this).val());
        
    });
    
    
    $(selection_box).html(selectii);
    
    $('#filter_category').val([]);
    $('#filter_category').val(selectiivals);
    
    if (selectii != '') $(selection_box).show();
    
}    


    
$('.multiple_selection_box').on('click', '.remove', function(){

   $('.category_tree option[value='+$(this).parent().attr('item_id')+']').removeClass('selected'); 
    
   $('.multiple_selection_box button[item_id='+$(this).parent().attr('item_id')+']').remove();

   if (typeof $('.multiple_selection_box button').html() == 'undefined') $('.multiple_selection_box').hide();     
    
   refresh_selectionbox('.category_tree.multiple', '.multiple_selection_box');    
    
});  
    

    
function fillinPostSelection(cattree, selection_box) {
    
    selectii = '';
    selectiivals = [];
    
    $(cattree + ' option').each(function(){
        
        if ($(this).is(':selected')) {
            
            $(this).addClass('selected');
            
            selectii = selectii + '<button item_id="'+$(this).val()+'">'+$(this).text()+'<span class="remove">x</span></button>';
            
            selectiivals.push($(this).val());
            
        }
        
        
    });
    
    $(selection_box).html(selectii);
    
    $('#filter_category').val([]);
    
    $('#filter_category').val(selectiivals);
    
    if (selectii != '') $(selection_box).show();
    
}    
    
    

fillinPostSelection('.category_tree.multiple', '.multiple_selection_box');    

    
    
});