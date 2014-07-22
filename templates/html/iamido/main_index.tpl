<div class="row">
    <div class="col-xs-3 col-sm-2" style="min-width: 220px;">
        <div class="input-group input-group-sm curved-vt-2" style="width: 210px;">
            {*<input id="add-task-datepicker" class="form-control text-center datepickers" placeholder="add new task" title="Please, select a date to add a new task and press +." readonly="true" style="cursor: pointer;">*}
            <span class="input-group-btn input-group-sm">
                <span id="add-task-button" class="btn btn-default" style="width: 67px; border-radius: 4px;">
                    Add task {*<i class="glyphicon glyphicon-plus pull-right"></i>*}
                </span>
                <span id="del-task-button" class="btn btn-default" style="width: 67px; border-radius: 4px;" disabled>
                    Del task
                </span>
                <span id="upd-task-button" class="btn btn-default" style="width: 67px; border-radius: 4px;" disabled>
                    Upd task
                </span>
            </span>
            </span>
        </div>
        <div class="curved-vt-2" style="width: 210px; border: 1px solid #bbb; border-radius: 4px; background-color: #eee;">
            <input id="task-id" class="form-control text-center" placeholder="enter task id" title="Enter a task id or select it from the table by clicking on ID field." style="width: 200px; margin-left: auto; margin-right: auto;  margin-top: 5px;">
            <h4 align="center">Manual time entry:</h4>
            <div class="input-group input-group-sm" style="width: 200px; margin-left: auto; margin-right: auto;  margin-top: 5px;">
                <input type="time" id="auto-start-time" class="form-control text-center" style="width: 83px;" title="Time from"> 
                <input type="time" id="auto-finish-time" class="form-control text-center" style="width: 83px;" title="Time to">
                <span class="input-group-btn input-group-sm">
                    <span class="btn btn-default">
                        <i class="glyphicon glyphicon-floppy-save"></i>
                    </span>
                </span>
            </div>
            <h4 align="center">Automatic counting:</h4>
            <div class="form-control text-center" style="width: 200px; margin-left: auto; margin-right: auto;  margin-top: 5px;">-- days  -- hours  -- minutes</div>
            <br>
            <div class="onoffswitch" style="margin-left: auto; margin-right: auto;">
                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch">
                <label id="switch" class="onoffswitch-label" for="myonoffswitch">
                    <span class="onoffswitch-inner"></span>
                    <span class="onoffswitch-switch"></span>
                </label>
            </div>
        </div> 
    </div>
    <div class="col-xs-12 col-sm-6 col-md-8" style="">
        <div class="lifted" style="min-width: 570px;">
            <h1 class="text-center" style="margin-top: 10px; font-weight: bold; color: #888; word-spacing:20px; text-shadow: rgb(3, 3, 3) 0px 0px 2px;">I AM - I DO</h1>
        </div>
        <div class="raised" style="min-width: 570px;">
            <table id="organizer" class="table-hover table table-bordered table-responsive">
                <thead class="list">
                    <tr class="top-table" style="height: 25px; cursor: text;">
                        <th rowspan="2">ID</th>
                        <th rowspan="2">Status</th>
                        <th rowspan="2">Task</th>
                        <th rowspan="2">Definition</th>
                        <th rowspan="2">Start Date</th>
                        <th rowspan="2">Deadline</th>
                        <th colspan="2">Time</th>
                        <th rowspan="2">BIZ</th>
                    <tr class="top-table" style="height: 25px; cursor: text;">
                        <th>Budget</th>
                        <th>Used</th>
                    </tr>
                </thead>
                <tbody>
                    {* тело таблицы *}
                    {include file="templates/html/iamido/control_index.tpl"}
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-xs-3 col-sm-2 visible-lg" style="">
        <div class="pull-right btn-group curved-vt-2" style="width: 210px;">
            <button type="button" class="btn btn-default dropdown-toggle" style="width: 200px;" data-toggle="dropdown"> Quick Navigation  <span class="caret"></span></button>
            <ul class="dropdown-menu" role="menu" style="width: 210px;">
                <li><a href="/orders">View Orders</a></li>
                <li><a href="/positions">Positions</a></li>
                <li><a href="/items">Items</a></li>
                <li class="divider"></li>
                <li><a href="/ra">RA</a></li>
                <li><a href="/bizes">BIZ search</a></li>
            </ul>
        </div> 
        <div class="pull-right raised text-center" style="width: 210px; margin-top: 0px; border: 5px solid #bbb; border-radius: 60px;">
            <strong>Current task:</strong><br>
            I am what i do<br>
            <strong>Definition:</strong><br>
            Создание контроллера, модели "I am what i do". Написание базовых функций.<br>
            <strong>Remained time:</strong><br>
            <span id="remained-time"></span>
        </div>
        
        
    </div>
</div>

{* модальное окно редактирования *}
{*include file="templates/html/iamido/control_edit.tpl"*}

<div id="my-modal" class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">New task</h4>
            </div>
                <div class="modal-body">
                    {* Cкрытый input для хранения task_id *}
                    <input id="hidden-task-id" class="hidden">
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
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button id="save-button" type="button" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>
    