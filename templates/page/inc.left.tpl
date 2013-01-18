<div class="well well-small clearfix" style="margin-bottom: 0.5em;">
	{if $page}
		<a href="/page/show/{$page.parentPageId}" class="btn btn-info" title="Go to parent page"><i class="icon-arrow-up"></i></a>
		<a href="/page/edit/{$page.id}" class="btn btn-warning pull-right" style="margin-left: 0.5em;" title="Edit this page"><i class="icon-pencil"></i></a>
		{if !$subPages}
			<a href="/page/remove/{$page.id}" onclick="return confirm('Delete this page?')" class="btn btn-danger pull-right" style="margin-left: 0.5em;" title="Delete this page"><i class="icon-trash"></i></a>
		{/if}
	{/if}
	<a href="/page/create/{if $page}{$page.id}{else}0{/if}" class="btn btn-primary pull-right" title="Add a sub-page"><i class="icon-plus-sign"></i></a>
</div>
{if $subPages}
	<ul class="_pages-sortable nav nav-tabs nav-stacked" style="background-color: #f8f8f8;">
		{foreach name=subPages from=$subPages item=subPage}
			<li id="subpage-{$subPage.id}">
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

