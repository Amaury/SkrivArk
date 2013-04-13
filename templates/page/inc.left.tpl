<div class="well well-small clearfix" style="margin-bottom: 0.5em;">
	{if $page}
		<a href="/page/show/{$page.parentPageId}" class="btn btn-info" title="Go to parent page"><i class="icon-arrow-up"></i></a>
		<a href="/page/edit/{$page.id}" class="btn btn-warning pull-right" style="margin-left: 0.5em;" title="Edit this page"><i class="icon-pencil"></i></a>
		{if $ACTION != "versions"}
			<button class="btn btn-inverse pull-right" style="margin-left: 0.5em;" title="Move this page" onclick="$('#modal-move').modal('show')"><i class="icon-move icon-white"></i></button>
		{/if}
		{if !$subPages}
			<a href="/page/remove/{$page.id}" onclick="return confirm('Delete this page?')" class="btn btn-danger pull-right" style="margin-left: 0.5em;" title="Delete this page"><i class="icon-trash"></i></a>
		{/if}
		{if $ACTION != "versions" && $page.nbrVersions > 1}
			<a href="/page/versions/{$page.id}" class="btn btn-info pull-right" style="margin-left: 0.5em;" title="View the {$page.nbrVersions} versions of the page"><i class="icon-tasks"></i></a>
		{elseif $ACTION == "versions"}
			<a href="/page/show/{$page.id}" class="btn btn-info pull-right" style="margin-left: 0.5em;" title="See the last version of the page"><i class="icon-eye-open"></i></a>
		{/if}
	{/if}
	<a href="/page/create/{if $page}{$page.id}{else}0{/if}" class="btn btn-primary pull-right" title="Add a sub-page"><i class="icon-plus-sign"></i></a>
</div>
{if $ACTION == "versions" && $versions}
	{* ****************** VERSIONS OF A PAGE ********************** *}
	<script type="text/javascript"><!--{literal}
		function loadPage(versionId, url) {
			$("#link-version-" + versionId).attr("href", url);
			document.location.href = url;
		}
	{/literal}//--></script>
	<ul class="nav nav-tabs nav-stacked" style="background-color: #f8f8f8;">
		{foreach name="versions" from=$versions item=version}
			<li {if $version.id == $versionFrom}class="active"{/if}
			    {if $version.id == $versionTo}class="active"{/if}>
				<a id="link-version-{$version.id}"
				 {if $version.id == $versionFrom || $version.id == $versionTo}href="#" onclick="return false"
				 {else}href="/page/versions/{$page.id}/{$versionFrom}/{$version.id}"{/if}
				 {if $version.id == $versionFrom}title="This is the version of reference" style="background-color: #ddffdd;"
				 {elseif $version.id == $versionTo}title="This is the compared version" style="background-color: #fdd;"
				 {else}title="Define the version to compare"{/if}>
					{if $smarty.foreach.versions.index}
						<i class="icon-pencil pull-right" onclick="loadPage({$version.id}, '/page/edit/{$page.id}/{$version.id}')"
						 title="Edit the page from this version" style="margin-left: 4px; cursor: pointer;"></i>
					{else}
						<span class="pull-right" style="width: 18px;">&nbsp;</span>
					{/if}
					{if $version.id == $versionFrom}
						<i class="icon-plus"></i>
						<span class="pull-right" style="width: 18px;">&nbsp;</span>
					{elseif $version.id == $versionTo}
						<i class="icon-minus"></i>
						<span class="pull-right" style="width: 18px;">&nbsp;</span>
					{else}
						<span class="pull-left" style="width: 18px;">&nbsp;</span>
						<i class="icon-map-marker pull-right" title="Define the version of reference"
						 onclick="loadPage({$version.id}, '/page/versions/{$page.id}/{$version.id}/{$versionTo}')"
						 tyle="margin-left: 4px;"></i>
					{/if}
					{$version.creationDate|date_format:"%Y-%m-%d %H:%M"} - {$version.name|escape}
				</a>
			</li>
		{/foreach}
	</ul>
	<div style="margin-left: 5em; font-size: 0.7em; color: #888;">
		<i class="icon-plus"></i> Version of reference<br />
		<i class="icon-minus"></i> Compared version<br />
		{if $versions|@count > 2}
			<i class="icon-map-marker"></i> Use this version as comparison reference<br />
			<i class="icon-hand-up"></i> Click on a version to use it for comparison<br />
		{/if}
		<i class="icon-pencil"></i> Edit the page from this version
	</div>
{elseif $subPages}
	{* ****************** SUBPAGES OF A PAGE ********************** *}
	<ul class="_pages-sortable nav nav-tabs nav-stacked" style="background-color: #f8f8f8;">
		{foreach name=subPages from=$subPages item=subPage}
			<li id="subpage-{$subPage.id}" title="{$subPage.intro|escape}">
				<a href="/page/show/{$subPage.id}">
					<i class="icon-chevron-right pull-right" style="opacity: 0.5;"></i>
					{$subPage.title|escape}
				</a>
			</li>
			{if !$smarty.foreach.subPages.last}
				<li class="divider"></li>
			{/if}
		{/foreach}
	</ul>
	{if $subPages|@count > 1}
		<div style="text-align: center; font-size: 0.7em; color: #888;">You can order subpages using drag n'drop.</div>
	{/if}
{/if}
{* drag n'drop init *}
<script type="text/javascript">{literal}<!--
	$("._pages-sortable").sortable({
		axis: "y",
		cursor: "move",
		delay: 150,
		helper: "clone",
		update: function() {
			var order = $(this).sortable('toArray');
			$.post("/page/setPriorities/" + {/literal}{if $page}{$page.id}{else}0{/if}{literal}, {prio: order});
		}
	});
//-->{/literal}</script>

