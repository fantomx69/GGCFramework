{include file="v_header.tpl" title=Prova}

<h1>Log In</h1>
<form method="post" action="{$smarty.server.PHP_SELF}?page=login">
    <table>
        <tr><td>Nome :</td><td><input type="text" name="Name"/></td></tr>
        <tr><td>Password :</td><td><input type="text" name="Password"/></td></tr>
        <tr><td><input type="submit" name="login"/></td></tr>
    </table>
</form>

{include file="v_footer.tpl"}