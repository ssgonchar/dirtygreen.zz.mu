<!---------------------Page Name---------------------------------->
		
<div class="pull-left" style='width: 200px; margin-right: 15px;'>
    <a  href='/nomenclature/'><button class="btn btn-default" type="button" style='width: 200px; height: 35px; margin-bottom: 10px;' >View all list</button></a>
		<!-----function выводит список категорий - меню (accordeon)----------------->
	{function name=categories_tree}
            {if $categorylist}
		{foreach from=$categorylist item=row}
                    <div class="panel-group" id="accordion" style='width: 200px; margin-bottom: 10px;' >
			<div class="panel panel-default">
                            <div class="panel-heading">
				<h4 class="panel-title" style='cursor: pointer;'>
                                    <a class='category-link' onclick="show_nomenclature(event, {$row.category.url});" data-toggle="collapse" data-parent="#accordion">
					{$row.category.title}
                                    </a>
				</h4>
                            </div>
                            <div class="panel-collapse collapse out">
				{if $row.sub_categories}
                                    {foreach from=$row.sub_categories key=key1 item=sub_row}
					<a class="panel-body" onclick="show_nomenclature(event, {$sub_row.category.url});" style='display: block; height: 25px; line-height: 0px; cursor: pointer;'>
                                            {$sub_row.category.title}
					</a>
                                    {/foreach}
				{/if} 
                            </div>
			</div>
                    </div>
		{/foreach}
            {/if}
	{/function}
	{categories_tree categorylist=$categorylist}
		<!--------------------------------------------------------------------------->
</div>
		
		<!---------------------Таблица номенклатур ---------------------------------->
<br><table class="list search-target" width="80%" style='clear: right;'>
	<thead id='nomenclature-thead'>
		<tr class="top-table" style='cursor: default;'>
			<th width="10%">Title</th>
			<th width="40%">Description</th>
			<th width="7%">Modified</th>
			<th width="1%"></th>
		</tr>
	</thead>
		{include file='templates/html/nomenclature/control_nomenclature.tpl'}
</table>
		<!--------------------------------------------------------------------------->
	
		<!----------------- Модальное окно добавления номенклатуры ------------------>
<div class="modal fade" id='modal-add'>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Adding new nomenclature</h4>
      </div>
      <div class="modal-body">
	{include file='templates/html/nomenclature/main_add.tpl'}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="submit" name="btn_save" class="btn btn-primary" value="Save" onclick="return confirm('Are you sure?');">Save</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
		<!--------------------------------------------------------------------------->
		
		<!--------------------- Модальное окно редактирования номенклатуры ---------->
<div class="modal fade" id='modal-edit'>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Editing of nomenclature</h4>
      </div>
      <div class="modal-body">
	    <ul class="panel panel-default"><!-- Форма редактирования  ---->
		<li class="panel-heading">
		    <h3 class="panel-title">Please, fill the form</h3>
		</li>
		<br>
			<!-- Здесь поля title & Description вставляются функцией edit_nomenclature()  ---->
			<li class='edit-category'> 
			    <div class="input-group">
				<span class="input-group-addon">Select a category:</span>
				<select style="width:100%" class="form-control" name="form[category_id]">
			<!-- Функция выводит список категорий для занесения в базу. Параметр category_id -->
				    {function name=edit_categories_tree}
					{if $categorylist}
					    {foreach from=$categorylist item=row}	
						<option value="{$row.category.url}" style="color: blue; font-weight: bold;"> --- {$row.category.title}</option>
						{if $row.sub_categories}
						    {foreach from=$row.sub_categories key=key1 item=sub_row}
							<option value="{$sub_row.category.id}">{$sub_row.category.title}</option>
						    {/foreach}
						{/if}
					    {/foreach}
					{/if}
				    {/function}
				    {edit_categories_tree categorylist=$categorylist}
			<!--------------------------------------------------------------------------------->
				</select>
			    </div>
			</li>
	    </ul><!-- /Форма редактирования -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="submit" name="btn_save" class="btn btn-primary" value="Save" onclick="return confirm('Are you sure?');">Save</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
		<!-------------------------------------------------------------------------->
