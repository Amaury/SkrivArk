{include file="inc.miniHeader.tpl"}

<header class="app-header app-header-dark bg-black">
	<div class="top-bar">
		<div class="top-bar-brand">
			<a href="/" class="d-flex"><span>
				{if $conf.sitename}{$conf.sitename|escape}{else}SkrivArk{/if}
			</span></a>
		</div>
		<div class="top-bar-list">
			{* hamburger menu *}
			<div class="top-bar-item px-2 d-md-none d-lg-none d-xl-none">
				<button class="hamburger hamburger-squeeze" type="button" data-toggle="aside" aria-label="Menu"><span class="hamburger-box"><span class="hamburger-inner"></span></span></button>
			</div>
			{* search *}
			<form class="form-inline mt-2 mt-md-0 d-none d-md-block" method="get" action="/page/search">
				<input class="form-control mr-sm-2" type="text" name="s" placeholder="Search" aria-label="Search" value="{$s|escape}" onfocus="this.select()">
			</form>
			{* header links *}
			<div class="top-bar-item top-bar-item-right pr-3 pr-lg-4">
				<ul class="header-nav nav">
					{if $user}
						{if $user.admin}
							<li class="nav-item">
								<a class="nav-link" href="/admin"><span class="d-sm-inline">Admin</span></a>
							</li>
						{/if}
						<li class="nav-item">
							<a class="nav-link" href="/authentication/account"><span class="d-sm-inline">My account</span></a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="/authentication/logout"><span class="d-sm-inline">Logout</span></a>
						</li>
					{else}
						<li class="nav-item">
							<a class="nav-link" href="/authentication/login"><span class="d-sm-inline">Login</span></a>
						</li>
					{/if}
				</ul>
			</div>
		</div>
	</div>
</header>
