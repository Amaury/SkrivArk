{include file="inc.miniHeader.tpl"}

<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container-fluid">
			<a href="/" class="brand">{if $conf.sitename}{$conf.sitename|escape}{else}SkrivArk{/if}</a>
			{if $user.admin}
				<ul class="nav">
					<li {if $CONTROLLER == "admin"}class="active"{/if}><a href="/admin">Admin</a></li>
				</ul>
			{/if}
			<ul class="nav pull-right">
				{if $user}
					<li><a href="/identification/account">My account</a></li>
					<li><a href="/identification/logout">Logout</a></li>
				{else}
					<li><a href="/identification/login">Login</a></li>
				{/if}
			</ul>
		</div>
	</div>
</div>

