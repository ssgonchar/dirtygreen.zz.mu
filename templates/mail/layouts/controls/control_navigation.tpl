<ul id="menu">
    <li>
        <a href="#">
        {if isset($smarty.session.user.person)}
            {if isset($smarty.session.user.person.picture)}{picture type="person" size="x" source=$smarty.session.user.person.picture}
            {else}<img src="/img/layout/anonym{if $smarty.session.user.person.gender == 'f'}f{/if}.png" alt="No Picture">{/if}
        {else}<img src="/img/layout/anonym.png" alt="No Picture">{/if} {$smarty.session.user.login|escape:'html'}</a>
        <ul class="level-one">
            {if $onlinestatus == 'away'}
            <li class="dropdown"><a href="javascript: void(0);" class="away" onclick="set_user_status('online');" id="user-status-link">I'm away</a></li>
            {else}
            <li class="dropdown"><a href="javascript: void(0);" class="online" onclick="set_user_status('away');" id="user-status-link">I'm online</a></li>
            {/if}
            
            {if $smarty.session.user.role_id <= $smarty.const.ROLE_ADMIN && $smarty.session.user.id == 303}
            <li class="dropdown"><a href="#">Sys Admin</a>
                <ul class="level-two">
                    <li><a href="/item/removefromorder">Rem. Item f. Order</a></li>
                </ul>
            </li>
            {/if}
            <li class="dropdown"><a href="/logout">Logout</a></li>
        </ul>
    </li>
    <li><a href="#">Registers</a>
        <ul class="level-one">
            <li class="dropdown"><a href="/bizes">BIZ</a>
                <ul class="level-two">
                    <li><a href="/bizes">Search</a></li>
                    <li><a href="/biz/add">Add</a></li>
                </ul>
            </li>
            <li class="dropdown"><a href="/companies">Company</a>
                <ul class="level-two">
                    <li><a href="/companies">Search</a></li>
                    <li><a href="/company/add">Add</a></li>
                </ul>
            </li>
            <li class="dropdown"><a href="/markets">Markets</a></li>
            <li class="dropdown"><a href="/objectives">Objectives</a>
                <ul class="level-two">
                    <li><a href="/objectives/">Search</a></li>
                    <li><a href="/objective/add">Add</a></li>
                </ul>
            </li>
            <li class="dropdown"><a href="/persons">Person</a>
                <ul class="level-two">
                    <li><a href="/persons">Search</a></li>
                    <li><a href="/person/add">Add</a></li>
                    <li><a href="/persons/staff">STEELemotion Staff</a></li>
                    {if $smarty.session.user.role_id <= $smarty.const.ROLE_MODERATOR}<li><a href="/person/regrequests">Reg. Requests</a></li>{/if}
                </ul>
            </li>
            <li class="dropdown"><a href="/products">Product</a>
                {*
                <ul class="level-two">
                    <li><a href="/biz/add">Add Biz</a></li>
                </ul>
                *}
            </li>
            <li class="dropdown"><a href="/directory">Directories</a>
                <ul class="level-two">
                    {*<li><a href="/directory/dimensions">Dimension Units</a></li>*}
                    <li><a href="/directory/activities">Activities</a></li>
                    <li><a href="/directory/countries">Countries</a></li>
                    <li><a href="/directory/departments">Departments</a></li>
                    <li><a href="/directory/jobpositions">Job Positions</a></li>
                    <li><a href="/directory/steelgrades">Steel Grades</a></li>
                    <li><a href="/directory/teams">Teams</a></li>
                    {*<li><a href="/directory/weights">Weight Units</a></li>*}
                </ul>
            </li>
        </ul>
    </li>
    <li><a href="#">R & T</a></li>
    <li>
        <a href="#">Trade</a>
        <ul class="level-one">
            <li class="dropdown"><a href="/stocks">Stock</a>
                <ul class="level-two">
                    {* if isset($stat_stocks) && !empty($stat_stocks)}
                    {foreach from=$stat_stocks item=row}
                    <li><a href="/positions/filter/stock:{$row.stock.id};">{$row.stock.title|escape:'html'}</a></li>
                    {/foreach}
                    {/if *}
                    <li><a href="/positions">Positions</a></li>
                    <li><a href="/items">Items</a></li>
                    <li><a href="/stocks">Settings</a></li>
                    <li><a href="/stock/location">Locations</a></li>
                    <li><a href="/stockoffers">Offers</a></li>
                </ul>
            </li>
            <li class="dropdown"><a href="/orders">Orders</a>
                <ul class="level-two">
                    {if isset($stat_preorders_count) && !empty($stat_preorders_count)}
                    <li><a href="/orders/unregistered">Unregistered ({$stat_preorders_count})</a></li>
                    {/if}
                    <li><a href="/orders">List</a></li>
                    <li><a href="/order/neworder">Create Order</a></li>
                </ul>
            </li>            
            <li><a href="/positions/reserved">Reserve</a></li>
            <li class="dropdown"><a href="/reports">Reports</a>
                <ul class="level-two">
                    <li><a href="/report/stockcurrent">Current Stock Value</a></li>
                    <li><a href="/report/stockaudit">Stock Audit</a></li>
                    <li><a href="/report/stockinout">In / Out Report</a></li>
                </ul>
            </li>            
            <li><a href="/productionp">Prod. Possibilities</a></li> 
        </ul>        
    </li>
    <li>
        <a href="#">Contract Admin</a>
        <ul class="level-one">
            <li class="dropdown"><a href="/inddt">In DDT</a></li>
            <li class="dropdown"><a href="/supplierinvoices">Sup. Invoices</a></li>
            <li class="dropdown"><a href="/sc">SC</a></li>
            <li class="dropdown"><a href="/qc">QC</a></li>
            <li class="dropdown"><a href="/oc">Orig. Cert.</a></li>
            <li class="dropdown"><a href="/ra">RA</a></li>
            <li class="dropdown"><a href="/ddt">DDT</a></li>
            <li class="dropdown"><a href="/cmr">CMR</a></li>
            <li class="dropdown"><a href="/invoices">Invoices</a></li>
        </ul>        
    </li>
    <li>
        <a href="#">Communications</a>
        <ul class="level-one">
            <li class="dropdown"><a href="/touchline">TouchLine</a>
                <ul class="level-two">
                    <li><a href="/touchline">Today</a></li>
                    <li><a href="/touchline/mustdo">MustDO !</a></li>
                    <li><a href="/touchline/archive">Archive</a></li>
                    <li><a href="/touchline/search">Search</a></li>
                </ul>            
            </li>
            <li class="dropdown"><a href="/emails">eMails</a>
                <ul class="level-two">
                    <li><a href="/emails">Inbox</a></li>
                    <li><a href="/email/compose">Compose</a></li>
                    <li><a href="/email/filters">Filters</a></li>
                </ul>            
            </li>            
        </ul>
    </li>
</ul>


{*
<div class="navigation">
    <ul class="menu">
        <li>
            <div class="menu-structure"></div>
            <div class="border"></div> 
            <a href="/registers">Registers</a>
            <ul>
                <li><a href="/register/biz">Biz</a></li>
                <li><a href="/register/company">Company</a></li>
                <li><a href="/register/product">Product</a></li>                
            </ul>            
        </li>
        <li>
            <div class="menu-module"></div>
            <div class="border"></div> 
            <a href="/stock">Stock</a>
            <ul>
                <li>
                    <a href="/items">Items</a>
                    <ul>
                        <li><a href="/stock/item/add">Add Items</a></li>
                    </ul>                    
                </li>
                <li>
                    <a href="/positions">Positions</a>
                    <ul>
                        <li><a href="/position/add">Add Positions</a></li>
                    </ul>                    
                </li>
                <li>
                    <a href="/stocks">Stocks</a>
                    <ul>
                        <li><a href="/stock/add">Add Stock</a></li>
                    </ul>                    
                </li>
            </ul>            
        </li> 
        <li>
            <div class="menu-manual"></div>
            <div class="border"></div> 
            <a href="/directory">Directories</a>
            <ul>
                <li><a href="/directory/currencies">Currencies</a></li>
                <li><a href="/directory/dimensions">Dimension Units</a></li>
                <li><a href="/directory/locations">Locations</a></li>
                <li><a href="/directory/steelgrades">Steel Grades</a></li>
                <li><a href="/directory/weights">Weight Units</a></li>
            </ul>            
        </li>
    </ul><!-- .menu -->        
</div><!-- navigation -->
*}
