<form class="left-form">
    <div class="title">СПИСОК</div>
    <div class="text">
        <a href="/users/online"{if $page == 'online'} style="font-weight: bold;"{/if}>Сейчас на сайте</a>
    </div>
    <div class="text">
        <a href="/users"{if $page == 'list'} style="font-weight: bold;"{/if}>Все пользователи</a>
    </div>
    <div class="text">
        <a href="#">Администрация</a>
    </div>
    <div class="text">
        <a href="#">Забаненные</a>
    </div>
    <div class="title">ПОИСК</div>
    <div class="keyword">
        <input name="form[keyword]" type="text" value="{if isset($search_string)}{$search_string|escape:'html'}{/if}" class="key" />
        <input type="button" value="Найти пользователя" class="button-form" onclick="alert('Поиск еще разрабатывается...');"/>
    </div>    
</form>
<div class="publicity"></div><!--  заглушка рекламного блока -->
