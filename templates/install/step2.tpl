{include file="inc.header.tpl"}

<script>{literal}
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
	function processCheckbox(checked) {
		if (checked)
			$("input[type='text']").attr("disabled", true);
		else
			$("input[type='text']").attr("disabled", false);
	}
{/literal}</script>

<main class="app-main" style="font-size: 1rem;">
	<div class="container" style="padding-top: 2.4rem;">
		<h2 style="margin-top: 40px;">Step 2 <small>Sessions management</small></h2>
		{if $cacheerror}
			<div id="panel-error" class="card bg-danger p-3">
				Unable to connect to the server. Please check your parameters.
			</div>
		{/if}
		<form method="post" action="/install/proceedStep2" onsubmit="return checkForm()" onkeypress="$('.form-group').removeClass('error'); $('#panel-error').hide()">
			<div class="form-group">
				<label>Server type</label>
				<div class="form-check">
					<input id="check-nocache" class="form-check-input" type="radio" name="cacheserver" value="nocache" checked
					 onchange="processCheckbox(this.checked)">
					<label class="form-check-label" for="check-nocache">
						No Memcache/Redis server
					</label>
				</div>
				<div class="form-check">
					<input id="check-memcache" class="form-check-input" type="radio" name="cacheserver" value="memcache"
					 onchange="processCheckbox(!this.checked); $('#edit-cacheport').val('11211');">
					<label class="form-check-label" for="check-memcache">
						Memcache
					</label>
				</div>
				<div class="form-check">
					<input id="check-redis" class="form-check-input" type="radio" name="cacheserver" value="redis"
					 onchange="processCheckbox(!this.checked); $('#edit-cacheport').val('6379');">
					<label class="form-check-label" for="check-redis">
						Redis
					</label>
				</div>
			</div>
			<div class="form-group">
				<label for="edit-cachehost">Hostname</label>
				<input id="edit-cachehost" type="text" name="cachehost" placeholder="localhost" disabled
				 value="{if $cachehost}{$cachehost|escape}{else}localhost{/if}" class="form-control" />
			</div>
			<div class="form-group">
				<label class="control-label" for="edit-cacheport">Port number</label>
				<input id="edit-cacheport" type="text" name="cacheport" placeholder="11211" disabled
				 value="{if $cacheport}{$cacheport|escape}{else}11211{/if}" class="form-control" maxlength="5" />
			</div>
			<div class="form-group">
				<input type="submit" class="btn btn-primary" value="Proceed to step 3" />
			</div>
		</form>
	</div>
</main>

{include file="inc.footer.tpl"}
