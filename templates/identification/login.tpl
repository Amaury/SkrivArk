{assign var="coloredBackground" value=true}
{include file="inc.miniHeader.tpl"}

<div class="container">
	<h1 style="text-align: center;">{if $conf.sitename}{$conf.sitename|escape}{else}SkrivArk{/if}</h1>
	<form method="post" action="/identification/auth" class="form-signin">
		<h2 class="form-signin-heading">Sign in</h2>
		<input id="email" type="text" name="email" placeholder="Email" value="{$emailLogin}" class="input-block-level" />
		<input type="password" name="password" placeholder="password" class="input-block-level" />
		<input type="submit" value="Sign in" class="btn btn-large btn-primary" />
	</form>
</div>

{include file="inc.miniFooter.tpl"}
