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

<div class="container-fluid">
	<div class="row-fluid">
		{* left column *}
		<div class="span3">
			{include file="page/inc.left.tpl"}
		</div>
		{* main content *}
		<div class="span9">
			{if !$page}
				{* homepage *}
				<div class="hero-unit">
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
				{* breadcrumb *}
				{if ($page && !$subPages) || $user}
					<ul class="breadcrumb">
						<li><a href="/" title="Home"><i class="icon-home"></i></a>{if $page.parentPageId} <span class="divider">/</span>{/if}</li>
						{foreach name=breadcrumb from=$breadcrumb item=crumb}
							<li>
								<a href="/page/show/{$crumb.id}">{$crumb.title|escape}</a>
								{if !$smarty.foreach.breadcrumb.last}
									<span class="divider">/</span>
								{/if}
							</li>
						{/foreach}
					</ul>
				{/if}
				{* content *}
				<div id="content" xclass="well">
					{* title *}
					<h1>{$page.title|escape}</h1>
					{$page.html}
				</div>
				{* subscription *}
				{if $user}
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
					</form>
				{/if}
				{* Disqus comments *}
				{if $conf.disqus}
					<div id="disqus_thread"></div>
				{/if}
			{/if}
		</div>
	</div>
</div>

{include file="inc.footer.tpl"}
