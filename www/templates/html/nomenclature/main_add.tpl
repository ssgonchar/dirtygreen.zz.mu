
<ul class="panel panel-default">
    <li class="panel-heading">
        <h3 class="panel-title">Please, fill the form</h3>
    </li>
        <br>
        <ul style='padding: 5px;'>
            <li>
                <div class="input-group">
                    <span class="input-group-addon">Title: &nbsp&nbsp &nbsp&nbsp &nbsp&nbsp &nbsp</span>
                    <input name="form[title]" type="text" class="form-control">
                </div>
            </li>
                <br>
            <li>
                <div class="input-group">
                    <span class="input-group-addon">Description:</span>
                    <textarea id='add-description' name="form[description]" class="form-control" style="height: 150px;"></textarea>
                </div>
            </li>
                <br>
            <li> 
                <div class="input-group">
                    <span class="input-group-addon">Select a category:</span>
                    <select style="width:100%" class="form-control" name="form[category_id]">
            <!-------- Функция выводит список категорий для занесения в базу. Параметр category_id ---------->
                        {function name=categories_tree}
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
                        {categories_tree categorylist=$categorylist}
            <!---------------------------------------------------------------------------------------------->
                    </select>
                </div>
            </li>
        </ul>
        <br>
</ul>
  
