{include file='inc.header.tpl'}

{include file='page/inc.left.tpl'}

<main class="app-main" style="font-size: 1rem;">
	<div class="wrapper">
		<div class="page has-sidebar has-sidebar-expand-xl">
			<div class="page-inner">
				<header class="page-title-bar">
					<h1 class="page-title">{$titleDiff}</h1>
				</header>
				<div id="content" class="page-section">
					<pre style="white-space: pre-wrap;">{$skrivDiff}</pre>
				</div>
			</div>
		</div>
	</div>
</main>

{include file='inc.footer.tpl'}
