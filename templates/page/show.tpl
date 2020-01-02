{include file="inc.header.tpl"}

{include file="page/inc.modal-move.tpl"}

{include file="page/inc.left.tpl"}

<main class="app-main" style="font-size: 1rem;">
	<div class="wrapper">
		<div class="page has-sidebar has-sidebar-expand-xl">
			{* page content *}
			<div class="page-inner">
				{if !$page}
					{* homepage *}
					<div id="content" class="page-section nocount">
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
						<h1>
							{if $conf.allowReadOnly && $conf.allowPrivatePages && $page.isPrivate}
								<i class="fas fa-lock" title="This page is private. Only loggued users can see it."></i>
							{/if}
							{$page.title|escape}
						</h1>
					</header>
					{* content *}
					<div id="content" class="page-section {if $page.nocount}nocount{/if}">
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
					{* Table of Contents *}
					{if is_array($page.toc) && ($page.toc|@count > 1 || (is_array($page.toc[0].sub) && $page.toc[0].sub|@count > 1))}
						<nav id="nav-content" class="nav flex-column mt-4">
							{for $index=0 to $page.toc|@count}
								{$item = $page.toc[$index]}
								{if $item.type == 'h2'}
									<a href="#{$item.id|escape}" class="nav-link smooth-scroll">{$item.value}</a>
								{elseif $item.type == 'h3'}
									<blockquote>
										<a href="#{$item.id|escape}" class="nav-link smooth-scroll">{$item.value}</a>
										{for $subindex=$index+1 to $page.toc|@count}
											{$item = $page.toc[$subindex]}
											{if $item.type == 'h3'}
												<a href="#{$item.id|escape}" class="nav-link smooth-scroll">{$item.value}</a>
											{else}
												{$index = $subindex - 1}
												{break}
											{/if}
										{/for}
									</blockquote>
								{/if}
							{/for}
						</nav>
					{/if}
				</div>
			</div>
		</div>
	</div>
</main>

<script>{literal}
	$(document).ready(function() {
		// prettyprint
		$("#content pre").addClass("prettyprint");
		PR.prettyPrint();
	});
{/literal}</script>

{include file="inc.footer.tpl"}
