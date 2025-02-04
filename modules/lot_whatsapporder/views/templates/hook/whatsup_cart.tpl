{*
* 2020 Ecomdoo Software SRL
*
* @author Ecomdoo Software SRL (LOT.ai)
* @copyright 2020 Ecomdoo Software SRL
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
   

<div id="whatsuporder">
       
    <a href="//api.whatsapp.com/send?phone={$LOT_WHATSAPP_NUMBER}&text={$LOT_WHATSAPP_TEXT}" target="_blank">
        <span>{if $LOT_WHATSAPP_BUTTON_TEXT}{$LOT_WHATSAPP_BUTTON_TEXT}{else}{l s='Order with WhatsApp' mod='lot_whatsapporder'}{/if}</span>
    </a>

</div>