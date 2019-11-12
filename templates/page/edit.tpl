{include file='inc.header.tpl'}

{* modal dialog window for SkrivML syntax cheat sheet *}
<div id="popup-syntax" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog mw-100 w-50" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">SkrivML syntax cheat sheet</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body"></div>
		</div>
	</div>
</div>

{* main body *}
<form id="form" method="post" style="margin: 0 0 20px!important; display: block;"
 {if $page}
	action="/page/storeEdit/{$page.id}"
 {else}
	action="/page/storeCreate/{$parentId}"
 {/if}>
	<div style="position: absolute; top: 96px; left: 0; right: 0;">
		<div class="container-fluid">
			<input id="edit-title" type="text" name="title" value="{$page.title|escape}" placeholder="Title" autocomplete="off" style="width: 48%;" />
			<input type="submit" class="btn btn-primary" value="{if $ACTION == "create"}Create the page{else}Save modifications{/if}" style="margin: -10px 0 0 10px;" />
			<a href="/page/show/{if $page}{$page.id}{else}{$parentId}{/if}" class="btn btn-secondary" style="margin: -10px 0 0 5px;">Cancel</a>
			<a href="http://markup.skriv.org/language/cheatSheet?blank=1" data-toggle="modal" data-target="#popup-syntax" style="float: right;">SkrivML syntax cheat sheet</a>
		</div>
	</div>

	<div id="body-content">
		<textarea id="skrivtext" name="content">{if $editContent}{$editContent}{else}{$page.skriv}{/if}</textarea>
		<div id="skrivhtml">{$page.html}</div>
	</div>
</form>

<script>{literal}
	$(document).ready(function() {
		var timer = null;
		// put focus on input zone
		if (!$("#edit-title").val().length)
			$("#edit-title").focus();
		else
			$("#skrivtext").focus();
		// text modification event
		$("#skrivtext").bind("input propertychange", function() {
			if (timer)
				clearTimeout(timer);
			timer = setTimeout(function() {
				var text = $("#skrivtext").val();
				var url = "/page/convert";
				$("#skrivhtml").load(url, {text: text});
			}, 300);
		});
	});
{/literal}</script>

{include file='inc.footer.tpl'}
