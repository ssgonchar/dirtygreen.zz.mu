<!-- Fixed navbar -->
<div class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"></a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                {*<li><a href="/">Dashboard</a></li>*}
                <li class="dropdown">
                    <a  class="dropdown-toggle js-activated" data-toggle="dropdown" href="#">Trade <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="/orders">View orders</a></li>                        
                        <li><a href="/order/neworder">Create order</a></li>      
                        <li><a href="/stockoffers">Offers</a></li>
                        <li class="divider"></li>
                        <li><a href="/positions">Positions</a></li>
                        <li><a href="/items">Items</a></li>
                        <li><a href="/productionp">Prod. Possibilities</a></li>          
                        <li><a href="/stocks">Stock</a></li>
                        <li><a href="/stock/location">Locations</a></li>                                        
                            {if isset($stat_preorders_count) && !empty($stat_preorders_count)}
                            <li class="divider"></li>    
                            <li><a href="/orders/unregistered">Unregistered <span class="badge"> {$stat_preorders_count} </span></a></li>
                            {/if}


                    </ul>                    
                </li>       

                <li class="dropdown">
                    <a  class="dropdown-toggle js-activated" data-toggle="dropdown" href="#">Administration <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="/ddt">Out DDTs</a></li>            
                        <li><a href="/inddt">In DDTs</a></li>
                        <li><a href="/cmr">CMRs</a></li>
                        <li><a href="/ra">Release advices</a></li>
                        <li><a href="/supplierinvoices">Supplier invoices</a></li>
                        <li><a href="/invoices">Invoices</a></li>
                        <li><a href="/sc">Sale confirmations</a></li>
                        <li><a href="/qc">Quality certificates</a></li>
                        <li><a href="/oc">Original certificates</a></li>     

                    </ul>                    
                </li> 				

                <li class="dropdown">
                    <a  class="dropdown-toggle js-activated" data-toggle="dropdown" href="#">Reports <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="/positions/reserved">Reserve</a></li>
                        <li><a href="/report/stockcurrent">Current Stock Value</a></li>
                        <li><a href="/report/stockaudit">Stock Audit</a></li>
                        <li><a href="/report/stockinout">In / Out Report</a></li>
                        <li><a href="/item/audit">Stock Archive</a></li>

                    </ul>                    
                </li> 

                <li class="dropdown">
                    <a  class="dropdown-toggle js-activated" data-toggle="dropdown" href="#">Registers <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="/bizes">BIZ search</li>
                        <li class="divider"></li>
                        <li><a href="/companies">Company search</a></li>
                        <li class="divider"></li>
                        <li><a href="/objective">Objectives search</a></li>
                        <li><a href="/objective/add">Objectives add</a></li>
                        <li class="divider"></li>
                        <li><a href="/persons">Person search</a></li>
                            {*<li><a href="/person/add">Person add</a></li>*}
                        <li><a href="/persons/staff">Our team</a></li>
                        {if $smarty.session.user.role_id <= $smarty.const.ROLE_MODERATOR}<li><a href="/person/regrequests">Registration requests </a></li>{/if}
                        <li class="divider"></li>  
                        <li><a href="/markets">Markets</a></li>
                        <li><a href="/products">Product</a></li>                            
                    </ul>                    
                </li>                
                <li class="dropdown">
                    <a  class="dropdown-toggle js-activated" data-toggle="dropdown" href="#">Communications<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="/touchline">TouchLine</a></li>
                        <li class="divider"></li> 
                        <li><a href="/emails/filter/type:0;">Emails manager</a></li>
                        <li><a href="/emailmanager/filter/type:0;">Emails manager(NEW)</a></li>
                        <li><a href="/email/filters">Emails filters</a></li>
                    </ul>                    
                </li>  
                <li class="dropdown">
                    <a  class="dropdown-toggle js-activated" data-toggle="dropdown" href="#">Directories <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="/directory/activities">Activities</a></li>
                        <li><a href="/directory/countries">Countries</a></li>
                        <li><a href="/directory/departments">Departments</a></li>
                        <li><a href="/directory/jobpositions">Job Positions</a></li>
                        <li><a href="/directory/steelgrades">Steel Grades</a></li>
                        <li><a href="/directory/teams">Teams</a></li>                
                        <li><a href="/nomenclature/">Navigation</a></li>                         
                    </ul>                    
                </li>  

            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <div class="btn-group">    
                        

                      <a href="/touchline">  <button class="btn btn-link bg-success" onclick="location.href = '/touchline';" style='margin-top: 10px;' title="Touchline">
                            <span class="glyphicon glyphicon-comment"></span>
                          </button></a>
                         <a href="">
                        <button class="btn btn-link bg-success" onclick="show_chat_modal('chat', 0);" style='margin-top: 10px;' title="Message">
                            <span class="glyphicon glyphicon-pencil"></span>
                        </button></a>
                         <a href="/touchline/mustdo">
                        <button class="btn btn-link"  onclick="location.href = '/touchline/mustdo';" style='margin-top: 10px; height: 28px; vertical-align: middle;'  title="Todo list">
                            <span class="glyphicon glyphicon-list-alt"></span> <small class="count-pending"></small>
                        </button> </a>          
                         <a href="/email/compose">
                        <button class="btn btn-link" onclick="location.href = '/email/compose';" style='margin-top: 10px;'   title="Send email">
                            <span class="glyphicon glyphicon-envelope"></span> 
                        </button>  </a>
                         <a href="/positions">
                        <button class="btn btn-link" onclick="location.href = '/positions';" style='margin-top: 10px;'   title="Positions">
                            <span class="glyphicon glyphicon-align-justify"></span> 
                        </button>  </a>
                        
                         <a href="/">
                        <button class="btn btn-link bg-success" onclick="location.href = '/';" title="Dashboard" style='margin-top: 10px; height: 28px; vertical-align: middle;'>
                            <span class="glyphicon glyphicon-th-large"></span>
                        </button> </a>                       
                    </div>  

                    <div class="dropdown search-on-page-dropdown" style="display: inline-block;">
                        <button title='Search on page' class=" dropdown-toggle btn btn-link btn-search-on-page" style="margin-top: 10px;" data-toggle="dropdown">
                            <span class="glyphicon glyphicon-search"></span>
                        </button>

                        <ul class="dropdown-menu search_box" style="padding: 10px;min-width: 250px;">
                            <li>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p>Search on page <input class="always_show" type="checkbox" {if $smarty.session.user_settings.search_on_page.always_show == 'true'}checked{/if} > always open</p>
                                        <form class="form" role="form" method="post" action="login" accept-charset="UTF-8" id="login-nav">
                                            <div class="input-group search-on-page">
                                                <span class="input-group-btn">
                                                    <button disabled id="prev_search" class="btn btn-primary" onclick="return false;"><span class="glyphicon glyphicon-arrow-left"></span></button>
                                                </span>
                                                <input id="search_text" class="form-control" type="text" placeholder="keyword" {if $smarty.session.user_settings.search_on_page.always_show == 'true'}value="{$smarty.session.user_settings.search_on_page.query}"{/if}/>
                                                <span class="input-group-btn">
                                                    <button disabled id="next_search" class="btn btn-primary" onclick="return false;"><span class="glyphicon glyphicon-arrow-right"></span></button>
                                                    <button disabled id="clear_button" class="btn btn-danger" onclick="return false;"><span class="glyphicon glyphicon-remove"></span></button>
                                                </span>        
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="count" style="font-size:10pt;"></div>
                                    </div>                                    
                                </div>
                            </li>
                        </ul>
                    </div>                                                                

                    <!-- Single button -->
                    <div class="btn-group">
                        <button type="button" class="btn btn-link dropdown-toggle js-activated" data-toggle="dropdown"  style='margin-top: 10px;'><span class="glyphicon glyphicon-user"></span> <span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/person/{$smarty.session.user.person_id}/edit">My profile</a></li>
                                {if $smarty.session.user.id === "1682"}
                                <li><a href="/iamido" title="">I am what I do</a></li>
                                {/if}
                            <li class="divider"></li>
                            <li><a href="http://mamtrix.steelemotion.com" target="_blank" title='Mamtrixt'>Mamtrix (old system)</a></li>
                            <li><a href="/logout">Logout</a></li>
                            {*<li class="divider"></li>

                                    {foreach from=$smarty.session['__core:request_cache'] item=row}
                                    <li><a href="/{$row}">{$row}</a></li>
                                    {/foreach}*}

                        </ul>
                    </div>

                </li>
                <li>        
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                            <span class="sr-only">Navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a href="#" style="margin-left: 5px; border: 1px solid #6a727d; border-radius: 3px;  margin-top: 4px; display: inline-block;">
                            {if isset($smarty.session.user.person)}
                                {if isset($smarty.session.user.person.picture)}{picture type="person" size="x" source=$smarty.session.user.person.picture}
                                {else}<img src="/img/layout/anonym{if $smarty.session.user.person.gender == 'f'}f{/if}.png" alt="No Picture">{/if}
                            {else}<img src="/img/layout/anonym.png" alt="No Picture">{/if} </a>
                    </div>
                </li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>
