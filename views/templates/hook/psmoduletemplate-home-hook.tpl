<!-- displayHome module -->
<div id="psmoduletemplate_block_left" class="block">
  <h4>Welcome! homeHook</h4>
  <div class="block_content">
  <p>note: this is hook/psmoduletemplate-home-hook.tpl</p>
    <p>Hello, 
       {if isset($my_module_name) && $my_module_name}
           {$my_module_name}
       {else}
           World
       {/if}
       !        
    </p>
    <p>{$my_module_message}</p>
       {if isset($my_module_linkHome) && $my_module_linkHome}
           <p><a class="myLink" href="{$my_module_linkHome}" title="Click this link">Click me!</a></p>
       {/if}

  </div>
</div>
<!-- /displayHome module -->
