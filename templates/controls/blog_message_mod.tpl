<div class="panel panel-default" class="biz-blog-tl-block biz-blog-entity">
  <!-- Default panel contents -->
  <div class="panel-heading" class="biz-blog-tl-head">
      {if $row.message.sender_id == $smarty.const.GNOME_USER}
                    <img src="/img/layout/gnome.jpg" alt="Gnome" alt="Gnome">
                {elseif isset($row.message.sender) && isset($row.message.sender.person)}
                    {if isset($row.message.sender.person.picture)}{picture type="person" size="x" source=$row.message.sender.person.picture}
                    {elseif $row.message.sender.person.gender == 'f'}<img src="/img/layout/anonymf.png" alt="{$row.message.sender.login}" alt="{$row.message.sender.login}">
                    {else}<img src="/img/layout/anonym.png" alt="{$row.message.sender.login}" alt="{$row.message.sender.login}">{/if}
                {else}
                    <img src="/img/layout/anonym.png" alt="No Picture" alt="No Picture">
                {/if} 
                <b>{$row.message.title|parse}</b>
                
                <span style="display:inline-block;float:right; text-align: right;"><i style='cursor: pointer;'  data-message-id="{$row.message_id}" data-created-at="{$row.message.created_at|date_format:"d/m/Y"}">{$row.message.created_at|date_format:"d/m/Y"}&nbsp;{$row.message.created_at|date_format:"H:i:s"}</i>&nbsp;           {if $smarty.session.user.role_id <= $smarty.const.ROLE_STAFF && !empty($row.message.is_pending) && isset($row.message.is_pending_recipient) && (empty($row.message.userdata) || empty($row.message.userdata.done_at))}
                <div style="float: right; margin-right: 0px;" id="message-{$row.message.id}-pending" class="biz-blog-tl-deadline" onclick="mark_message_as_done({$row.message.id});">
                {if !empty($row.message.deadline)}
                    Deadline: {$row.message.deadline|date_format:'d/m/Y'}
                {else}
                    MustDO !
                {/if}
                </div>
            {/if}</span> 
  </div>
  <!-- Table -->
  <table class="table">
    <tr>
        <td>
            <b>Route</b>
        </td>
        <td>
                           {if $row.message.type_id == $smarty.const.MESSAGE_TYPE_PRIVATE || $row.message.type_id == $smarty.const.MESSAGE_TYPE_PERSONAL}
                    <i>(p)</i>&nbsp;
                {/if}
                {$row.message.sender.login}&nbsp;&rarr;&nbsp;
                {if !isset($row.message.recipient) || empty($row.message.recipient)}
                    MaM
                {else}
                    {foreach from=$row.message.recipient item=r name=r}{$r.user.login}{if !$smarty.foreach.r.last}/{/if}{/foreach}{if !empty($row.message.cc)}.cc.{foreach from=$row.message.cc item=c name=c}{$c.user.login}{if !$smarty.foreach.c.last}/{/if}{/foreach}{/if}
                {/if}             
        </td>
      </tr>
{if isset($row.message.attachments) && !empty($row.message.attachments)} 
            <tr>
        <td>
            <b>Attachments</b>
        </td>
        
     <td>
        <div class="biz-blog-tl-attachments">
            {foreach from=$row.message.attachments item=att}
                {include file='templates/html/dropbox/control_attachment_block_text.tpl' attachment=$att.attachment readonly=true}        
            {/foreach}
        </div> 
        </td>
            </tr>
    {/if}             
        
      
  </table>
          <div class="panel-body" style="background: #ffc; color:#333; font-size: 15px;">
              <p class="lead"><small>{$row.message.description|parse|nl2br}</small></p>
  </div>
</div>
  