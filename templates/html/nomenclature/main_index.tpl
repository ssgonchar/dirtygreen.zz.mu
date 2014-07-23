<!---------------------Page Name---------------------------------->
		
<div class="col-md-3" style="border-right: 1px solid #ccc;">
		<!-----function выводит список категорий - меню (accordeon)----------------->
	{function name=categories_tree}
            {if $categorylist}
		{foreach from=$categorylist item=row}
                    <div class="panel-group" id="accordion" style="margin-bottom: 5px;">
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
		<!----/function ----------------------------------------------------------------------->
</div>
<div class="col-md-9">
<form id="form">

        <!--<div class="panel panel-default">-->
            <input id="nomenclature-id" style="display: none;">
            <textarea id="nomenclature-description" style="display: none;"></textarea>
            

                    <p>
                        <span><strong>Title: </strong></span><span id="title"></span>
                    </p>
                    <hr/>
                    <p>
                        
                        <textarea id="description-input" class="form-control" placeholder="text" type="text" style="width: 100%; height: 150px;"></textarea>
                    </p>
                    <div class="pull-right">
                        <span id="displayer" class="text-success text-right"></span>&nbsp;<input type="button" name="" value="Save" class="btn btn-success">
                    </div>

              


</form>                    
                </div>

		<!--------------------- Форма редактирования --------------------------
