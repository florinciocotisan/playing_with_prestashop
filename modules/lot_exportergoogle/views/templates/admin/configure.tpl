{*
* 2018 Ecomdoo Software SRL
*
* @author Ecomdoo Software SRL (LOT.ai)
* @copyright 2018 Ecomdoo Software SRL
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{addJsDef modulepath=$module_dir|escape:'html':'UTF-8'}
<script>
var itemslang = "{l s='items' mod='lot_exportergoogle'}";
var name_error = "{l s='Name field is empty!' mod='lot_exportergoogle'}";
var feedcontent_error = "{l s='Feed structure is empty!' mod='lot_exportergoogle'}";
</script>

  <div class="variableconfigurator_bg"> </div>  
<input type="hidden" id="sodcheiesig" value="{$secure_key|escape:'html':'UTF-8'}" />
<div class="panel">
	<div class="row moduleconfig-header">
		<div class="col-xs-12 text-center">
		   <img src="{$module_dir|escape:'html':'UTF-8'}logo.png" height="60px" />
			<h2>{l s='Exporter & Feed Management for Google Merchant Center' mod='lot_exportergoogle'}</h2>
			<h4>{l s='This module allows you to export your products to Google Merchant Center (xml,csv).' mod='lot_exportergoogle'}</h4>
		</div>
		
		
	</div>

	
</div>


<div id="lotmyProgress">
    <label class="emptymessage">{l s='There are no products corresponding to your criteria at this moment. The feed template was created but it contains no products!' mod='lot_exportergoogle'}</label>
    <label class="inprocess">{l s='Your feed is being processed!' mod='lot_exportergoogle'}</label>
    <label class="finished">{l s='Your feed is ready!' mod='lot_exportergoogle'}</label>
    <div id="lotmyBarcontainer">
      <div id="lotmyBar"></div>
    </div>
    <div id="lotmyBarNumbers"></div>
    <div id="viewfeed"><a href='#' target="_blank">{l s='View feed' mod='lot_exportergoogle'}</a>
        <div class="clearfix"></div>
    <button id="closeprogress">{l s='Close' mod='lot_exportergoogle'}</button>
    </div>
    
</div>


{if !empty($existingfeeds)}
<div class="panel" id="existingfeeds">
    <legend>{l s='Your templates' mod='lot_exportergoogle'}</legend>	
<div class="row moduleconfig-content">
			<div class="col-xs-12">

 <div id="deleteddok">{l s='The feed template was successfully deleted ! The page will now reload!' mod='lot_exportergoogle'}</div>    
                                                                                                                                                               
          <div id="deletederror">{l s='ERROR: The feed template was not deleted !' mod='lot_exportergoogle'}</div>                                                                                     						
			
<table class="table std">
<tr><th>{l s='ID' mod='lot_exportergoogle'}</th><th>{l s='Name' mod='lot_exportergoogle'}</th><th>{l s='Type' mod='lot_exportergoogle'}</th><th>{l s='Date add' mod='lot_exportergoogle'}</th><th>{l s='Action' mod='lot_exportergoogle'}</th></tr>
{foreach from=$existingfeeds item=f}
<div id="editheader_{$f.id_feed}" class="hide">{$f.header}</div>
<div id="editcode_{$f.id_feed}" class="hide">{$f.editcode}</div>
{if $f.type == 'csv'}
<div id="csvcode_{$f.id_feed}" class="hide">{$f.code}</div>
{/if}
<div id="editfooter_{$f.id_feed}" class="hide">{$f.footer}</div>

<input type="hidden" id="editfeedid_{$f.id_feed}" value="{$f.id_feed}">
<input type="hidden" id="editfeedname_{$f.id_feed}" value="{$f.name}">
<input type="hidden" id="editfeedtype_{$f.id_feed}" value="{$f.type}">
<input type="hidden" id="editvalueseparator_{$f.id_feed}" value="{$f.valueseparator}">
<input type="hidden" id="editfielseparator_{$f.id_feed}" value="{$f.fielseparator}">
    <tr><td>{$f.id_feed}</td><td>{$f.name}</td><td>{$f.type}</td><td>{$f.date_add}</td><td><a href="?controller=AdminLotExporterGoogle&token={$pagetoken}&editfeed={$f.id_feed}"><img src="../img/admin/edit.gif" title="{l s='Edit feed template' mod='lot_exportergoogle'}" /></a><a href="{$feed_link}&id_feed={$f.id_feed}" target="_blank"><img src="../img/admin/details.gif" title="{l s='See feed online' mod='lot_exportergoogle'}" /></a><a href="{$download_link}&id_feed={$f.id_feed}" target="_blank"><img src="../img/admin/arrow_down.png" title="{l s='Download feed' mod='lot_exportergoogle'}" /></a>
        <img class="sodgetlinkbtn" src="../img/admin/external_link.png" title="{l s='Get direct link to feed' mod='lot_exportergoogle'}" onclick="getdirectlink('{$f.id_feed}')" /><img class="soddetelebtn" src="../img/admin/delete.gif" title="{l s='Delete template and feed' mod='lot_exportergoogle'}" onclick="deletefeed('{$f.id_feed}')" /></td></tr><tr id="alert_feed-{$f.id_feed}" class="alertrow"><td colspan="5"><label>{l s='The direct link to feed is:' mod='lot_exportergoogle'}</label><input type="text" value=""/></td></tr>
{/foreach}
                </table>			
    </div>
    </div> 

    
    
<div id="cronlink" class="alert alert-info">    
    
    <p>{l s='In order for your feeds to be up to date and contain all the changes made to your product catalog you have to add the link bellow to your cron tasks. You can do that from your server(cpanel) cron jobs section or by using the Prestashop "Cron tasks manager" module with webservice activated. If you don\'t know how contact your hosting service provider or your shop developer.' mod='lot_exportergoogle'}</p>
    <p><a href="{$cron_link}" target="_blank">{$cron_link}</a></p>   
        
</div>                                                            
                                                                    
       			   			       			   			
</div>
{/if}

<div class="panel" id="filterproducts">
    <legend>{l s='Matching with Google Categories' mod='lot_exportergoogle'}</legend>	
<div class="row moduleconfig-content">
    <div class="col-xs-12"> 
            <div id="button_to_google_match">    
               {if (!Configuration::get('LOT_EXPORTERGOOGLE_MAIN_GCID'))}
               <p class="alert alert-warning">{l s='You haven\'t configured the main Google category. Please do in order to avoid rejection of your catalog in Google Merchant Center. Use the button bellow.' mod='lot_exportergoogle'}</p>
                {else if (Configuration::get('LOT_EXPORTERGOOGLE_MAIN_GCID') == 0)}
                <p class="alert alert-warning">{l s='You haven\'t configured the main Google category. Please do in order to avoid rejection of your catalog in Google Merchant Center. Use the button bellow.' mod='lot_exportergoogle'}</p>
               {/if}
                <p><b>{l s='For better listing on Google Merchant Center is highly recommended to match your shop categories with Google categories, if you haven\'t already done this. Click the button below.' mod='lot_exportergoogle'}</b></p>
                <a href="{$googlmatch_controller_link}" target="_blank"><button class="button btn exclusive"><span>{l s='Matching with Google Categories' mod='lot_exportergoogle'}</span></button></a>            
            </div>   

    </div>
    </div>
</div>          

<div class="panel" id="filterproducts">   

    <legend>{l s='Filter products' mod='lot_exportergoogle'}</legend>	
<div class="row moduleconfig-content">
			<div class="col-xs-12">
			
    <input type="checkbox" {if !empty($restrictions)}{if $restrictions.only_active == 1}checked="checked"{/if}{else}checked="checked"{/if} id="onlyactive" value="1" /><label>{l s='Only active products' mod='lot_exportergoogle'}</label>
    <div class="clear"></div>	
<input type="checkbox" id="combsep" value="0" {if !empty($restrictions)}{if $restrictions.combsep == 1}checked="checked"{/if}{/if} /><label>{l s='Consider product combination as separate product' mod='lot_exportergoogle'}	</label>        		
			<div class="clear"></div>	
<p class="info">{l s='If no category, manufacturer, feature or attribute is selected, all products will be included in the feed.' mod='lot_exportergoogle'}</p>
	<div class="clear"></div>			
<label>{l s='Category' mod='lot_exportergoogle'}</label>
<div class="multiple_selection_box"></div>	
<div class="clear"></div>		
<select id="filter_category" class="category_tree multiple" data-style="btn-primary" multiple data-max-options="100">
{if !empty($allcategories)}
{foreach from=$allcategories item=c}
<option value="{$c.id|escape:'html':'UTF-8'}">{$c.name|escape:'html':'UTF-8'}</option>
    {if !empty($c.children)}
    {foreach from=$c.children item=cc}
    <option class="cc {if !empty($cc.children)}withsubs{/if}" value="{$cc.id|escape:'html':'UTF-8'}" {if !empty($restrictions)}{if in_array($cc.id,$restrictions.filter_category)}selected="selected"{/if}{/if} >{$cc.name|escape:'html':'UTF-8'}</option>
    {if !empty($cc.children)}
    {foreach from=$cc.children item=ccc}
    <option class="ccc children-{$cc.id} {if !empty($ccc.children)}withsubs{/if}" value="{$ccc.id|escape:'html':'UTF-8'}" {if !empty($restrictions)}{if in_array($ccc.id,$restrictions.filter_category)}selected="selected"{/if}{/if}>{$ccc.name|escape:'html':'UTF-8'}</option>
    {if !empty($ccc.children)}
    {foreach from=$ccc.children item=cccc}
    <option class="cccc children-{$ccc.id} {if !empty($cccc.children)}withsubs{/if}" value="{$cccc.id|escape:'html':'UTF-8'}" {if !empty($restrictions)}{if in_array($cccc.id,$restrictions.filter_category)}selected="selected"{/if}{/if}>{$cccc.name|escape:'html':'UTF-8'}</option>
    
    {if !empty($cccc.children)}
    {foreach from=$cccc.children item=ccccc}
    <option class="ccccc children-{$cccc.id}" value="{$ccccc.id|escape:'html':'UTF-8'}" {if !empty($restrictions)}{if in_array($ccccc.id,$restrictions.filter_category)}selected="selected"{/if}{/if}>{$ccccc.name|escape:'html':'UTF-8'}</option>
    {/foreach}
    {/if}
    
    {/foreach}
    {/if}
    {/foreach}
    {/if}
    {/foreach}
    {/if}
{/foreach}
{/if}
</select>
<small>{l s='multiple selection allowed' mod='lot_exportergoogle'}</small>        
   <div class="clear"></div>	             		
<label>{l s='Manufacturer' mod='lot_exportergoogle'}</label>			
<select id="filter_manufacturer" class="selectpicker" data-style="btn-primary" multiple data-max-options="100">	
   {if !empty($allmanufacturers)}
   {foreach from=$allmanufacturers item=m}
    <option value="{$m.id_manufacturer|escape:'html':'UTF-8'}" {if !empty($restrictions)}{if in_array($m.id_manufacturer,$restrictions.filter_manufacturer)}selected="selected"{/if}{/if}>{$m.name|escape:'html':'UTF-8'}</option>
    {/foreach}
   {/if}
</select>   
<small>{l s='multiple selection allowed' mod='lot_exportergoogle'}</small>                   		
 <div class="clear"></div>	       
<label>{l s='Feature' mod='lot_exportergoogle'}</label>			
<select id="filter_feature" class="selectpicker" data-style="btn-primary" multiple data-max-options="100">
    {if !empty($allfeatures)}
    {foreach from=$allfeatures item=f}
    <optgroup label="{$f.feature}">
    {if !empty($f.values)}
    {foreach from=$f.values item=fv}
    <option value="{$fv.val}" {if !empty($restrictions)}{if in_array($fv.val,$restrictions.filter_feature)}selected="selected"{/if}{/if}>{$fv.display}</option>
    {/foreach}
    {/if}
    {/foreach}
    {/if}
</select>   
<small>{l s='multiple selection allowed' mod='lot_exportergoogle'} *{l s='only predefined values included' mod='lot_exportergoogle'}</small>

 <div class="clear"></div>	
  <label>{l s='Attribute' mod='lot_exportergoogle'}</label>			
<select id="filter_attribute" class="selectpicker" data-style="btn-primary" multiple data-max-options="100">	
     {if !empty($allattributes)}
    {foreach from=$allattributes item=f}
    <optgroup label="{$f.name}">
    {if !empty($f.values)}
    {foreach from=$f.values item=fv}
    <option value="{$fv.val}" {if !empty($restrictions)}{if in_array($fv.val,$restrictions.filter_attribute)}selected="selected"{/if}{/if}>{$fv.display}</option>
    {/foreach}
    {/if}
    {/foreach}
    {/if}
</select>   
<small>{l s='multiple selection allowed' mod='lot_exportergoogle'}</small>
<div class="clear"></div>	
 

   
    </div>
    </div>
</div>    		       		
			
  


<div class="panel" id="feedoptions">   

<legend>{l s='Feed options' mod='lot_exportergoogle'}</legend>	
<div class="row moduleconfig-content">
<div class="col-xs-12">
    
<label>{l s='Product ID prefix'  mod='lot_exportergoogle'}</label>  
<input type="text" id="idprefix" class="smallinput" value="{if isset($feedsettings.idprefix)}{$feedsettings.idprefix}{/if}" />             
<div class="clear"></div>	
    
<label>{l s='Currency'  mod='lot_exportergoogle'}</label>  
<select id="currency">
    {foreach from=Currency::getCurrencies() item=cur}
    <option value="{$cur.id_currency}" {if isset($feedsettings.currency)}{if $feedsettings.currency == $cur.id_currency}selected="selected"{/if}{/if}>{$cur.iso_code}</option>
    {/foreach}
</select>     
<div class="clear"></div>	
    
{if count($languages) > 0}
<label>{l s='Choose language'  mod='lot_exportergoogle'}</label>  
<select id="limba">
    {foreach from=$languages item=l}
    <option value="{$l.id_lang}" {if isset($feedsettings.id_lang)}{if $feedsettings.id_lang == $l.id_lang}selected="selected"{/if}{/if}>{$l.iso_code}</option>
    {/foreach}
                </select>                
<div class="clear"></div>	
{/if}    
    
    
<!--    
{if isset($googlecategories)}
<label>{l s='Choose Google Product ca'  mod='lot_exportergoogle'}</label>  
<select id="googleproductcategory" class="selectpicker" data-show-subtext="false" data-live-search="true">
    {foreach from=$googlecategories item=gc}
    <option value="{$gc}">{$gc}</option>
    {/foreach}
</select>                
<div class="clear"></div>	
{/if}
-->
    
{if isset($image_formats)}
<label>{l s='Image Type'  mod='lot_exportergoogle'}</label>  
<select id="image_formats" class="smallinput">
    {foreach from=$image_formats item=imf}
    <option value="{$imf.name}" {if isset($feedsettings.image_formats)}{if $feedsettings.image_formats == $imf.name}selected="selected"{/if}{/if}>{$imf.name}</option>
    {/foreach}
</select>                
<div class="clear"></div>	
{/if}    
 
<!--    
{if isset($attributes)}
<label>{l s='Choose Google Product ca'  mod='lot_exportergoogle'}</label>  
<select id="image_formats">
    {foreach from=$attributes item=atr}
    <option value="{$imf}">{$atr.name}</option>
    {/foreach}
</select>                
<div class="clear"></div>	
{/if}  
-->
    
                
</div> 
</div> 
</div>    


  

<div class="panel">   

    <legend>{l s='Configure feed' mod='lot_exportergoogle'}</legend>	
<div class="row moduleconfig-content">
			<div class="col-xs-12">
                
               
<div class="form-group">
   <input type="hidden" id="feedid" name="feedid" value="{if isset($feedid)}{$feedid}{else}0{/if}" />
    <label for="feedname">{l s='Name' mod='lot_exportergoogle'}</label>          
    <input type="text" id="feedname" name="feedname" value="" />
      
                </div>                                            
                
<div class="form-group">
    <label for="feedtype">{l s='Type' mod='lot_exportergoogle'}</label>              
    <select id="feedtype" name="feedtype">
        <option value="xml" {if isset($feedtype)}{if $feedtype == 'xml'}selected="selected"{/if}{/if}>{l s='xml' mod='lot_exportergoogle'}</option>
        <option value="csv" {if isset($feedtype)}{if $feedtype == 'csv'}selected="selected"{/if}{/if}>{l s='csv' mod='lot_exportergoogle'}</option>
    </select>          
</div> 			

						
    <div class="form-group" style="display:none;">
    <label for="feedheader">{l s='Header' mod='lot_exportergoogle'}</label>              
    <textarea id="feedheader" name="feedheader"></textarea>          
                </div> 
                
                        

               
                                               
                </div> 
                
                                                    
    <div class="form-group" style="display:none;">
    <label for="feedheader">{l s='Footer' mod='lot_exportergoogle'}</label>              
    <textarea id="feedfooter" name="feedfooter"></textarea>          
                </div> 
                                                                                                                                                              
                 
        <div id="feedsavedok">{l s='The feed template was successfully saved ! Now the feed is collecting data. Please wait!' mod='lot_exportergoogle'}</div>    
                                                                                                                                                               
          <div id="feedsavederror">{l s='ERROR: The feed template was not saved !' mod='lot_exportergoogle'}</div>                                                                                                                                                             
                                                                                                                                                                                                                                                                                                                                                                                                                                                           
      <div class="submit"> 
         <div class="waiting"><img src="{$module_dir|escape:'html':'UTF-8'}views/img/loading.gif"></div>          
          <button id="submitfeedformat" class="button btn exclusive" onclick="savefeed(0);"><span>{if isset($smarty.get.editfeed)}{l s='Save feed' mod='lot_exportergoogle'}{else}{l s='Create feed' mod='lot_exportergoogle'}{/if}</span></button> 
          {if isset($smarty.get.editfeed)}
          <button id="canceleditfeed" class="button btn exclusive" onclick="canceledit();"><span>{l s='Cancel' mod='lot_exportergoogle'}</span></button>{/if} 
        <button id="exportfile" class="button btn exclusive" onclick="savefeed(1);"><span>{l s='Export as file' mod='lot_exportergoogle'}</span></button>               
                </div>                                                                                                                                                                                                                                               
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       
                                                                                                                                                                
          
			</div>
		</div>

 