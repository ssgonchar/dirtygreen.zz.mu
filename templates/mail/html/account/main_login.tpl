<div class="pad"></div>
<div class="pagecontent">
    <form method="post" action="/login">
        <table class="form" cellpadding="5" cellspacing="1" border="0">
            {if !empty($err_msg)}
            <tr>
                <td colspan="2"><div id="msg" style="color:red; font-weight: bold; border: 1px solid red; background-color:#FEDEDE; padding:15px; margin-bottom:10px;">{$err_msg}</div></td>
            </tr>
            {/if}
            <tr>
                <td width="100px"><span class="label">Логин</span></td>
                <td width="100px"><input type="text" id="login" name="login" class="max"{if !empty($params.login)} value="{$params.login|escape:'html'}"{/if}></td>
            </tr>
            <tr>
                <td><span class="label">Пароль</span></td>
                <td><input type="password" id="password" name="password" class="max"></td>
            </tr>
            <tr>
                <td>{*
                    <a href="/remind">Восстановить&nbsp;пароль</a><br />
                    <a href="/register/">Регистрация</a>*}
                </td>
                <td align="right"><input type="submit" name="btn_login" class="btn100" value="Войти" /></td>
            </tr>
        </table>
    </form>
</div>