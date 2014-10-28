<div class="row">
    {*<div class="col-xs-3 col-sm-2" style="min-width: 220px;">*}
    <div class="" style="min-width: 220px;">
        <div class="input-group input-group-sm curved-vt-2" style="width: 210px;">
            <span class="input-group-btn input-group-sm">
                <span id="add-task-button" class="btn btn-default" style="width: 67px; border-radius: 4px;">
                    Add task
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
        <div id="task-control" class="curved-vt-2" style="width: 210px; border: 1px solid #bbb; border-radius: 4px; background-color: #eee;">
            <input id="task-id" class="form-control text-center" placeholder="enter task id" title="Enter a task id or select it from the table by clicking on ID field." style="height: 30px; width: 200px; margin-left: auto; margin-right: auto;  margin-top: 5px;">
            
            <select id="select-change-status" class="form-control chosen-select" data-placeholder="Change task status" style="height: 30px; width: 200px; background-color: #f5f5f5; margin-top: 5px; cursor: pointer;">
                <option id="nothing" class="text-center" selected disabled>Change task status</option>
                <option class="text-center" value="1">Waiting</option>
                <option class="text-center" value="2" style="background-color: #def0d8; text-align: center;">In process</option>
                <option class="text-center" value="3" style="background-color: #d9edf6;">Completed</option>
            </select>
            <h4 align="center">Manual time entry:</h4>
            <div class="input-group input-group-sm" style="width: 200px; margin-left: auto; margin-right: auto;  margin-top: 5px;">
                <div class="">
                    <div class="input-group input-group-sm" style="width: 200px;">
                        <input id="manual-start-data" class="form-control text-center manual-datepickers" placeholder="date of start" readonly="true" style="cursor: pointer; width: 115px;">
                        <input id="manual-start-time" type="time" class="form-control text-center start-time" style="width: 85px;"> 
                    </div>
                </div>
                <div class="">
                    <div class="input-group input-group-sm" style="width: 200px; margin-top: 5px;">
                        <input id="manual-finish-data" class="form-control text-center manual-datepickers" placeholder="date of finish" readonly="true" style="cursor: pointer; width: 115px;">
                        <input id="manual-finish-time" type="time" class="form-control text-center" style="width: 85px;"> 
                    </div>
                </div>
                
            </div>
                <span id="manual-save-time" class="btn btn-default btn-sm" style="margin-top: 5px; width: 200px">Save     
                    <i class="glyphicon glyphicon-floppy-save"> </i>
                </span>
            <h4 align="center">Automatic counting:</h4>
            <div id="auto-used-time" class="form-control text-center" style="height: 30px; width: 200px; margin-left: auto; margin-right: auto;  margin-top: 5px;">--</div>
            
            {* Переключатель on / off *}
            <fieldset class="switch" style="margin-left: auto; margin-right: auto; margin-top: 10px;">
                <label class="off">Off<input type="radio" class="on_off" name="on_off" value="off"/></label>
                <label class="on">On<input type="radio" class="on_off" name="on_off" value="on"/></label>
            </fieldset>
            
        </div> 
    </div>
    {*<div class="col-xs-12 col-sm-6 col-md-8" style="">*}
    <div class="" style="">
        <div class="lifted" style="min-width: 570px;">
            <h1 class="text-center" style="margin-top: 10px; font-weight: bold; color: #888; word-spacing:20px; text-shadow: rgb(3, 3, 3) 0px 0px 2px;">I am - what I do</h1>
        </div>
        <div class="raised" style="min-width: 570px;">
            <table id="organizer" class="table-hover table table-bordered table-responsive">
                {* заголовок таблицы *}
                <thead class="list">
                    <tr class="top-table" style="height: 25px; cursor: text;">
                        <th rowspan="2">Task ID</th>
                        <th id="th-status" rowspan="2" style="cursor: pointer;" title="Change view mode by status">Status  <span class="caret"></span></th>
                        <th rowspan="2">Title</th>
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
    {*<div class="col-xs-3 col-sm-2 visible-lg" style="">*}
    <div class="" style="">
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
<div id="my-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">New task
                    <i class="pull-right hidden glyphicon glyphicon-floppy-saved" style="margin-right: 15px;"></i></h4>
            </div>
                <div class="modal-body">
                    {* Cкрытый input для хранения task_id *}
                    <input id="hidden-task-id" class="hidden">
                    {* Cкрытый input для хранения status_id *}
                    <input id="hidden-status-id" class="hidden">
                    <span class="pull-left" style="width: 45%;">Date of start:</span>
                    <span class="pull-right" style="width: 45%;">Date of finish:</span>
                    {* Дата начала *}
                    <div class="pull-left">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input id="start-data" class="form-control text-center datepickers" placeholder="required field" readonly="true" style="cursor: pointer; width: 165px; margin-top: 0px;">
                            <input id="start-time" type="time" class="form-control text-center start-time" style="width: 85px; margin-top: 0px;"> 
                        </div>
                    </div>
                    {* Дата окончания *}
                    <div class="pull-right">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input id="finish-data" class="form-control text-center datepickers" placeholder="required field" readonly="true" style="cursor: pointer; width: 165px; margin-top: 0px;">
                            <input id="finish-time" type="time" class="form-control text-center" style="width: 85px; margin-top: 0px;"> 
                        </div>
                    </div>
                    
                    <div class="text-block" style="margin-top: 60px;">
                        <span class="pull-left" style="width: 65%; margin-top: 10px;">Title:</span>
                        <span class="pull-right" style="width: 20%; margin-top: 10px;">BIZ id:</span>
                        {* Title *}
                        <input id="task-title" class="form-control pull-left" placeholder="required field" style='width: 75%; margin-top: 0px;'>
                        {* BIZ id *}
                        <input id="biz-id" class="form-control pull-right" placeholder="" style='width: 20%; margin-top: 0px;'>
                        <br>
                        <br>
                        <br>
                        {* Definition *}
                        <span class="clearfix" style="width: 65%;"><br>Definition:</span>
                        <textarea id='task-definition' class="form-control clearfix" placeholder="required field" style="height: 120px; max-width: 558px; margin-top: 0px;"></textarea>
                        <br>
                        {* Personal notes *}
                        <span class="clearfix" style="width: 65%;">Personal notes:</span>
                        <textarea id='personal-notes' class="form-control" placeholder="" style="height: 100px; max-width: 558px; margin-top: 0px;"></textarea>
                    </div>
                </div>                 
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button id="save-button" type="button" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>
    