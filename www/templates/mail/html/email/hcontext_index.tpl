<div class="pad"></div>
    <table class="form" style="width: 100%;">
        <tr>
            <td><input type="text" name="form[keyword]" class="max"{if isset($keyword)} value="{$keyword}"{/if}></td>
            <td><input type="submit" name="btn_find" value="Find" class="btn100b"></td>
        </tr>
    </table>
            {*
    <div class="pad1"></div>
    <hr style="width: 100%; color: #dedede;" size="1"/>
    <div class="pad1"></div>
    *}
    <div style="font-weight: bold; color: black;">
    {if $list[0].email.type_id == $smarty.const.EMAIL_TYPE_SPAM || (!empty($page) && $page == 'deleted_by_user')}
        <a class="group-checkbox gc-all" href="javascript:void(0);" onclick="return false;" title="Select all emails" style="margin-right: 10px;">select all</a>
        <a class="group-checkbox gc-unselect" href="javascript:void(0);" onclick="return false;" title="Unselect emails" style="margin-right: 10px;">unselect all</a>
    {else}
        <a class="group-checkbox gc-all" href="javascript:void(0);" onclick="return false;" title="Select all emails" style="margin-right: 10px;">select all</a>
        <a class="group-checkbox gc-unread" href="javascript:void(0);" onclick="return false;" title="Select unreaded emails" style="margin-right: 10px;">select unread</a>
        <a class="group-checkbox gc-read" href="javascript:void(0);" onclick="return false;" title="Select readed emails" style="margin-right: 10px;">select read</a>
        <a class="group-checkbox gc-unselect" href="javascript:void(0);" onclick="return false;" title="Unselect emails" style="margin-right: 10px;">unselect all</a>
    {/if}
</div>
<div class="pad1"></div>