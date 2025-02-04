
{if $design.background_color != ''}
<style>
    #ecd_headeralert {
        background-color: {$design.background_color} !important;
    }    
</style>
{/if}

{if $design.color != ''}
<style>
     #ecd_headeralert, #ecd_headeralert p, #ecd_headeralert span, #ecd_headeralert a {
        color: {$design.color} !important;
    }  
    
</style>
{/if}

{if $design.font_size != ''}
<style>
     #ecd_headeralert, #ecd_headeralert p, #ecd_headeralert span, #ecd_headeralert a {
        font-size: {$design.font_size} !important;
    }    
</style>
{/if}



