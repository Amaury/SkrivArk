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
			<form id="form" method="post"
			 {if $ACTION == 'create'}
				action="/page/storeCreate/{$parentId}"
			 {else}
				action="/page/storeEdit/{$page.id}"
			 {/if}>
				<input id="hidden-check-private" type="hidden" name="private" value="{if $page.isPrivate}1{else}0{/if}" />
				<input id="hidden-check-nocount" type="hidden" name="nocount" value="{if $page.nocount}1{else}0{/if}" />
				<div class="page-inner" style="posititon: relative;">
					{* title *}
					<header class="page-title-bar">
						<input id="edit-title" type="text" name="title" class="form-control" style="font-size: 1.75rem; font-weight: 600;"
						 value="{if $ACTION == 'edit'}{$page.title|escape}{/if}" />
					</header>
					{* content *}
					<textarea id="edit-content" name="content" class="page-section">
						{if $ACTION == 'edit'}
							{if $editContent}
								{$editContent}
							{else}
								{$page.html}
							{/if}
						{/if}
					</textarea>
				</div>
			</form>
			{* content sidebar *}
			<div class="page-sidebar page-sidebar-fixed p-3" style="text-align: center;">
			</div>
		</div>
	</div>
</main>

<script type="text/javascript">{literal}
	// WYSIWYG init
	var summernoteOptions = {
		styleWithSpan: false,
		dialogsInBody: true,
		toolbar: [
			["style", ["style", "bold", "italic", "underline", "strikethrough", "clear"]],
			["color", ["color"]],
			["para", ["ul", "ol", "paragraph"]],
			["insert", ["link", "picture", "hr"]],
			["table", ["table"]],
			['view', ['codeview']],
			["misc", ["undo", "redo", "help"]]
		],
		popover: {
			table: [
				['add', ['addRowDown', 'addRowUp', 'addColLeft', 'addColRight', 'toggle']],
				['delete', ['deleteRow', 'deleteCol', 'deleteTable']],
				['custom', ['tableHeaders']]
			],
			image: [
				['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
				['float', ['floatLeft', 'floatRight', 'floatNone']],
				['remove', ['removeMedia']]
			]
		},
		styleTags: ['p', 'h2', 'h3', 'h4', 'pre', 'blockquote'],
		focus: false,
		minHeight: 150,
		maxHeight: null
	};
	$(document).ready(function() {
		var height = $(".page").height() - $("#edit-content").position().top - 75;
		summernoteOptions.minHeight = height;
		summernoteOptions.maxHeight = height;
		$("#edit-content").summernote(summernoteOptions);
		$("#edit-title").focus();
	});
	$(window).resize(function() {
		summernoteOptions.minHeight = 1;
		summernoteOptions.maxHeight = 1;
		let content = $("#edit-content").summernote('code');
		$("#edit-content").summernote('destroy');
		$("#edit-content").summernote(summernoteOptions);
		$("#edit-content").summernote('code', content);
		var height = $(".page").height() - $("#edit-content").position().top - 165;
		summernoteOptions.minHeight = height;
		summernoteOptions.maxHeight = height;
		content = $("#edit-content").summernote('code');
		$("#edit-content").summernote('destroy');
		$("#edit-content").summernote(summernoteOptions);
		$("#edit-content").summernote('code', content);
		return;
		$('div.note-editable').height(height);
	});
{/literal}</script>

{include file="inc.footer.tpl"}
