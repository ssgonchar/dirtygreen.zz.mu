
 {*<!--<div class="footer-left"{if !isset($target_doc)} style="width: 1000px;"{/if}>
    {include file="templates/html/chat/control_navigation.tpl" page="today"}
</div>
<div class="footer-right">
   
</div>

<div id="footer" class="container"">
    <nav class="navbar navbar-default navbar-fixed-bottom"  style="background: #b5bdc8; /* Old browsers */
background: -moz-linear-gradient(top,  #b5bdc8 0%, #828c95 36%, #28343b 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#b5bdc8), color-stop(36%,#828c95), color-stop(100%,#28343b)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  #b5bdc8 0%,#828c95 36%,#28343b 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  #b5bdc8 0%,#828c95 36%,#28343b 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  #b5bdc8 0%,#828c95 36%,#28343b 100%); /* IE10+ */
background: linear-gradient(to bottom,  #b5bdc8 0%,#828c95 36%,#28343b 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#b5bdc8', endColorstr='#28343b',GradientType=0 ); /* IE6-9 */
">
        <div class="navbar-inner navbar-content-center">
            <p class="text-muted credit">Example courtesy </p>
        </div>
    </nav>
</div>-->*}

<div class="navbar navbar-default navbar-fixed-bottom" role="navigation">
    <div class="container">
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav footer_panel">
{*
                <li>
					{if $page == 'pending'}
						<b>MustDo !{if isset($count) && !empty($count) && $page == 'pending'} ({$count}){/if}</b>
					{else}
						<a href="/touchline/mustdo">MustDO !{if isset($count) && !empty($count) && $page == 'pending'} ({$count}){/if}</a>
					{/if}				
				</li>
				<li>
					{if $page == 'search'}
						<b style="margin-left: 10px;">Search{if isset($count) && !empty($count) && $page == 'search'} ({$count}){/if}</b>
					{else}
						<a href="/touchline/search" style="margin-left: 10px;">Search{if isset($count) && !empty($count) && $page == 'search'} ({$count}){/if}</a>
					{/if}
				</li>     
				<li>
					{if $page == 'archive'}
						<b style="margin-left: 10px;">Archive{if !empty($count) && $page == 'archive'} ({$count}){/if}</b>
					{else}
						<a href="/touchline/archive" style="margin-left: 10px;">Archive{if !empty($count) && $page == 'archive'} ({$count}){/if}</a>
					{/if}
				</li>
				<li>
					{if $page == 'today'}
						<b style="margin-left: 10px;">Today</b>
					{else}
						<a href="/touchline" style="margin-left: 10px;">Today</a>
					{/if}
				</li>
*} 
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>	

