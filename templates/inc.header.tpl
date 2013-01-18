{include file="inc.miniHeader.tpl"}

<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container-fluid">
			<a href="/" class="brand">SkrivArk</a>
			{if $user.admin}
				<ul class="nav">
					<li {if $CONTROLLER == "admin"}class="active"{/if}><a href="/admin">Admin</a></li>
				</ul>
			{/if}
			<ul class="nav pull-right">
				<li><a href="/identification/logout">Logout</a></li>
			</ul>
		</div>
	</div>
</div>

