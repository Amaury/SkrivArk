{include file="inc.header.tpl"}

<div class="container-fluid">
	<div class="row-fluid">
		{* left column *}
		<div class="span3">
			{include file="page/inc.left.tpl"}
		</div>
		{* main content *}
		<div class="span9">
			{* breadcrumb *}
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
			{* title *}
			{*<h1>{$page.title|escape}</h1>*}
			<h1>{$titleDiff}</h1>
			{* content *}
			{*<div id="content" class="well">*}
			<pre>{$skrivDiff}</pre>
			{*</div>*}
		</div>
	</div>
</div>

{include file="inc.footer.tpl"}
