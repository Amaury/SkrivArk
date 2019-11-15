{assign var="coloredBackground" value=true}
{include file="inc.miniHeader.tpl"}

<div class="container">
	<h1 style="text-align: center;">{if $conf.sitename}{$conf.sitename|escape}{else}SkrivArk{/if}</h1>
	<form method="post" action="/authentication/auth" class="form-signin">
		<h2 class="form-signin-heading">Sign in</h2>
		<input id="email" type="text" name="email" placeholder="Email" value="{$emailLogin}" class="input-block-level" />
		<input type="password" name="password" placeholder="password" class="input-block-level" />
		<input type="submit" value="Sign in" class="btn btn-large btn-primary" />
		{if $conf.demoMode}
			<div class="well" style="margin-top: 2em; margin-bottom: 0;">
				<center>
					This is a demonstration site.<br />
					Its content is reset every hour.<br />
					Feel free to test it as you want to.
				</center>
				<br />
				<table style="margin: 0 auto;">
					<tr>
						<td align="right">Login:</td>
						<td rowspan="2">&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td><tt>demo@demo.com</tt></td>
					</tr>
					<tr>
						<td align="right">Password:</td>
						<td><tt>demo</tt></td>
					</tr>
				</table>
			</div>
		{/if}
	</form>
</div>

{include file="inc.miniFooter.tpl"}
