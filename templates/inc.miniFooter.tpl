</div>{* class="app" *}

<script src="/js/popper.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/theme.min.js"></script>
{* WYSIWYG editor *}
{if $CONTROLLER == 'page' && ($ACTION == 'edit' || $ACTION == 'create')}
	<script src="/vendors/summernote/summernote-bs4.js"></script>
	<script src="/js/summernote-fontawesome.js"></script>
	<script src="/js/summernote-table-headers.js"></script>
{else}
	<script src="/js/jquery-ui-1.9.2.custom.min.js"></script>
{/if}

{* /page/show *}
{if $URL == '/' || ($CONTROLLER == 'page' && $ACTION == 'show')}
	{* code prettifier *}
	<script src="/vendors/google-code-prettify/prettify.js"></script>
	{* drag n'drop *}
	{if $user}
		<script>{literal}
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
		{/literal}</script>
	{/if}
{/if}

</body>
</html>
