<div class="modalbox">
    <div class="m-overlay" onclick="close_modal();"></div>
    <div class="m-container">
        <div class="m-button close"><a href="javascript: void(0);" onclick="close_modal();">Close Window</a></div>
        <div class="m-content">{if !empty($modalbox_content)}{$modalbox_content}{/if}</div>
    </div>
</div>