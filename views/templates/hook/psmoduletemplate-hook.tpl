<!-- Block psmoduletemplate -->
<div id="psmoduletemplate_block_left" class="block">
  <h4>Welcome! hook</h4>
  <div class="block_content">
  <p>note: this is hook/psmoduletemplate-hook.tpl</p>
    <p>Hello, 
       {if isset($my_module_name) && $my_module_name}
           {$my_module_name}
       {else}
           World
       {/if}
       !        
    </p>
    <p>{$my_module_message}</p>
    <p><a class="myLink" href="{$my_module_link}" title="Click this link">Click me!</a></p>

  </div>
</div>
<!-- /Block psmoduletemplate -->
