{include file="inc.header.tpl"}
<style type="text/css">{literal}
	table.bordered {
		border: 1px solid #888;
	}
	table.bordered th {
		border: 1px solid #888;
		padding: 3px;
		background-color: #ddd;
	}
	table.bordered td {
		border: 1px solid #888;
		padding: 3px;
		background-color: #fff;
	}
	div.bordered {
		border: 1px solid #888;
		display: inline-block;
	}
{/literal}</style>

{include file="page/inc.modal-move.tpl"}

{include file="page/inc.left.tpl"}

<main class="app-main" style="font-size: 1rem;">
	<div class="wrapper">
		<div class="page has-sidebar has-sidebar-expand-xl">
			{* page content *}
			<div class="page-inner">
				{if !$page}
					{* homepage *}
					<div id="content" class="page-section">
						{if $splashscreen}
							{$splashscreen}
						{else}
							<h1><small>Welcome to</small> SkrivArk</h1>
							<p>
								A simple tool to create hierarchical pages of text.
							</p>
						{/if}
					</div>
				{else}
					{* title *}
					<header class="page-title-bar">
						<h1 class="page-title">{$page.title|escape}</h1>
					</header>
					{* content *}
					<div id="content" class="page-section">
						{$page.html}
					</div>
					{* Disqus comments *}
					{if $conf.disqus}
						<div id="disqus_thread"></div>
					{/if}
				{/if}
			</div>
			{* content sidebar *}
			<div class="page-sidebar page-sidebar-fixed">
				<header class="sidebar-header d-sm-none">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item active">
								<a href="#" onclick="Looper.toggleSidebar()"><i class="breadcrumb-icon fa fa-angle-left mr-2"></i>Back</a>
							</li>
						</ol>
					</nav>
				</header>
				<div class="sidebar-section-fill">
					{* subscription *}
					{if $user && $page}
						<div class="sidebar-body">
						<form class="form-inline">
							<label class="checkbox">
								<input type="checkbox" {if $page.subscribed}checked="checked"{/if}
								 {if $conf.demoMode}
									onchange="alert('[demo mode] This functionality is disabled')"
								 {else}
									onchange="ark.pageSubscription({$page.id}, $(this).is(':checked'))"
								 {/if} />
								Warn me when this page is modified
							</label>
						</form></div>
					{/if}
					{if is_array($page.toc) && ($page.toc|@count > 1 || (is_array($page.toc[0].sub) && $page.toc[0].sub|@count > 1))}
						<nav id="nav-content" class="nav flex-column mt-4">
							{foreach $page.toc as $item}
								<a href="#{$item.id|escape}" class="nav-link smooth-scroll">{$item.value}</a>
								{if $item.sub}
									<blockquote>
										{foreach $item.sub as $subitem}
											<a href="#{$subitem.id|escape}" class="nav-link smooth-scroll">{$subitem.value}</a>
										{/foreach}
									</blockquote>
								{/if}
							{/foreach}
						</nav>
					{/if}
				</div>
			</div>
		</div>
	</div>
</main>

{include file="inc.footer.tpl"}
