
{if !empty($current_task)}{debug}
    {foreach from=$current_task item=row}
        <div class="modal-body">
            {* Дата начала *}
            <div class="pull-left">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input id="start-data" class="form-control text-center datepickers" placeholder="date of start" readonly="true" style="cursor: pointer; width: 165px;">
                    <input id="start-time" type="time" class="form-control text-center start-time" style="width: 85px;"> 
                </div>
            </div>
            {* Дата окончания *}
            <div class="pull-right">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input id="finish-data" class="form-control text-center datepickers" placeholder="date of finish" readonly="true" style="cursor: pointer; width: 165px;">
                    <input id="finish-time" type="time" class="form-control text-center" style="width: 85px;"> 
                </div>
            </div>
            <br>
            <div class="" style="margin-top: 20px;">
                {* Title *}
                <input id="task-title" class="form-control pull-left" placeholder="task" style='width: 75%;'>
                {* BIZ id *}
                <input id="biz-id" class="form-control pull-right" placeholder="BIZ id" style='width: 20%;'>
                <br>
                <br>
                <br>
                {* Definition *}
                <textarea id='task-definition' class="form-control clearfix" placeholder="definition" style="height: 120px;"></textarea>
                <br>
                {* Personal notes *}
                <textarea id='personal-notes' class="form-control" placeholder="personal notes" style="height: 100px;"></textarea>
            </div>
        </div>
    {/foreach}
{/if}