{include file="inc.miniHeader.tpl"}

<header class="app-header app-header-dark bg-black">
	<div class="top-bar">
		<div class="top-bar-brand">
			<a href="/" class="d-flex"><span>
				{if $conf.sitename}{$conf.sitename|escape}{else}SkrivArk{/if}
			</span></a>
		</div>
		<div class="top-bar-list">
			<div class="top-bar-item px-2 d-md-none d-lg-none d-xl-none">
				{* hamburger menu *}
				<button class="hamburger hamburger-squeeze" type="button" data-toggle="aside" aria-label="Menu"><span class="hamburger-box"><span class="hamburger-inner"></span></span></button>
			</div>
			<div class="top-bar-item top-bar-item-right pr-3 pr-lg-4">
				<!-- .nav -->
				<ul class="header-nav nav">
					{if $user}
						{if $user.admin}
							<li class="nav-item">
								<a class="nav-link" href="/admin"><span class="d-sm-inline">Admin</span></a>
							</li>
						{/if}
						<li class="nav-item">
							<a class="nav-link" href="/identification/account"><span class="d-sm-inline">My account</span></a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="/identification/logout"><span class="d-sm-inline">Logout</span></a>
						</li>
					{else}
						<li class="nav-item">
							<a class="nav-link" href="/identification/login"><span class="d-sm-inline">Login</span></a>
						</li>
					{/if}
				</ul>
			</div>
		</div>
	</div>
</header>
