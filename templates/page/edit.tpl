{include file="inc.header.tpl"}

<div class="container">
	{* breadcrumb *}
	<ul class="breadcrumb">
		<li><a href="/"><i class="icon-home"></i></a>{if $breadcrumb} <span class="divider">/</span>{/if}</li>
		{foreach name=breadcrumb from=$breadcrumb item=crumb}
			<li>
				<a href="/page/show/{$crumb.id}">{$crumb.title|escape}</a>
				<span class="divider">/</span>
			</li>
		{/foreach}
		<li><a href="/page/show/{$page.id}"><strong>{$page.title|escape}</strong></a></li>
	</ul>
	<h1>
		{if $ACTION == 'create'}
			Creation
		{else}
			Edition
			{if $page.modifierId}
				<small>Last edition by {$page.modifierName|escape} on {$page.modifDate}</small>
			{else}
				<small>Created by {$page.creatorName|escape} on {$page.creationDate}</small>
			{/if}
		{/if}
	</h1>
	<div class="well">
		<form method="post"
		 {if $page}
			action="/page/storeEdit/{$page.id}"
		 {else}
			action="/page/storeCreate/{$parentId}"
		 {/if}>
			<div>
				<input type="text" name="title" value="{$page.title|escape}" placeholder="Title" autocomplete="off" style="width: 99%;" />
			</div>
			<textarea name="content" placeholder="Content" style="width: 99%; max-width: 99%; min-height: 5em; max-height: 60em; height: 30em;">{if $editContent}{$editContent}{else}{$page.skriv}{/if}</textarea>
			<div class="clearfix">
				<input type="submit" value="Save" class="btn btn-primary btn-large pull-right" />
				<a href="/page/show/{$page.id}" class="btn btn-inverse pull-right" style="margin-right: 1em;">Cancel</a>
			</div>
		</form>
	</div>
</div>

{include file="inc.footer.tpl"}
