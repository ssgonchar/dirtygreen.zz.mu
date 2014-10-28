                                                                            <select class="form-control find-biz-group-id" id="">
                                                                                <option value="0">/</option>
                                                                                {if isset($biz_menu) && !empty($biz_menu)}
                                                                                    {foreach from=$biz_menu item=row}
                                                                                        <option value="{$row.id}">{$row.title}</option>
                                                                                    {/foreach}
                                                                                {/if}	
                                                                            </select>