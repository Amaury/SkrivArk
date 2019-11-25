{include file='inc.header.tpl'}

{include file='page/inc.left.tpl'}

<main class="app-main" style="font-size: 1rem;">
	<div class="wrapper">
		<div class="page has-sidebar has-sidebar-expand-xl">
			<div class="page-inner">
				<div id="content" class="page-section">
					{foreach $result as $link}
						<strong><big><a href="/page/show/{$link.id}/{$link.title}">{$link.title|escape}</a></big></strong><br>
						{$link.excerpt}
						<hr>
					{foreachelse}
						<em>No result</em>
					{/foreach}
				</div>
			</div>
		</div>
	</div>
</main>

{include file='inc.footer.tpl'}
