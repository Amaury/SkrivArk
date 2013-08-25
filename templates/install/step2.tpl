{include file="inc.header.tpl"}

<script type="text/javascript">{literal}<!--
	function checkForm() {
		var ok = true;
		if (!/^[A-Za-z0-9_\.]+$/.test($("#edit-cachehost").val())) {
                        ok = false;
                        $("#edit-cachehost").parent().parent().addClass("error");
                }
		if (!/^[0-9]+$/.test($("#edit-cacheport").val())) {
			ok = false;
			$("#edit-cacheport").parent().parent().addClass("error");
		}
		return (ok);
	}
//-->{/literal}</script>

<div class="container">
	<h2 style="margin-top: 40px;">Step 2 <small>Memcache server</small></h2>
	{if $cacheerror}
		<div id="panel-error" class="alert alert-error">
			Unable to connect to the server. Please check your parameters.
		</div>
	{/if}
	<form method="post" action="/install/proceedStep2" onsubmit="return checkForm()" onkeypress="$('.control-group').removeClass('error'); $('#panel-error').hide()" class="form-horizontal">
		<div class="control-group">
			<label class="control-label" for="edit-cachehost">Hostname</label>
			<div class="controls">
				<input id="edit-cachehost" type="text" name="cachehost" placeholder="localhost"
				 value="{if $cachehost}{$cachehost|escape}{else}localhost{/if}" class="input-xlarge" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="edit-cacheport">Port number</label>
			<div class="controls">
				<input id="edit-cacheport" type="text" name="cacheport" placeholder="11211"
				 value="{if $cacheport}{$cacheport|escape}{else}11211{/if}" class="input-small" maxlength="5" />
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<input type="submit" class="btn btn-primary" value="Proceed to step 3" />
			</div>
		</div>
	</form>
</div>

{include file="inc.footer.tpl"}
