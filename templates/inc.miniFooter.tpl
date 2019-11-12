</div>{* class="app" *}

{* tracking Google Analytics *}
{if $conf.googleAnalytics}
<script>
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '{$conf.googleAnalytics}']);
  _gaq.push(['_trackPageview']);

{literal}
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
{/literal}
</script>
{/if}

<script src="/js/jquery-ui-1.9.2.custom.min.js"></script>
<script src="/js/popper.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/theme.min.js"></script>

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

</body>
</html>
