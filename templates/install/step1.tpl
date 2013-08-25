{include file="inc.header.tpl"}

{literal}
<script type="text/javascript">
	function checkForm() {
		var ok = true;
		var regex = /^[A-Za-z0-9_]+$/;
		if (!/^[A-Za-z0-9_\.]+$/.test($("#edit-hostname").val())) {
			ok = false;
			$("#edit-hostname").parent().parent().addClass("error");
		}
		if (!regex.test($("#edit-dbname").val())) {
			ok = false;
			$("#edit-dbname").parent().parent().addClass("error");
		}
		if (!regex.test($("#edit-user").val())) {
			ok = false;
			$("#edit-user").parent().parent().addClass("error");
		}
		if (!$("#edit-password").val().length) {
			ok = false;
			$("#edit-password").parent().parent().addClass("error");
		}
		return (ok);
	}
</script>
{/literal}

<div class="container">
	<h2 style="margin-top: 40px;">Step 1 <small>MySQL/MariaDB Database</small></h2>
	{if $dberror}
		<div id="panel-error" class="alert alert-error">
			Unable to connect to the database. Please check your parameters.
		</div>
	{/if}
	<form method="post" action="/install/proceedStep1" onsubmit="return checkForm()" onkeypress="$('.control-group').removeClass('error'); $('#panel-error').hide()" class="form-horizontal">
		<div class="control-group">
			<label class="control-label" for="edit-dbhostname">Hostname</label>
			<div class="controls">
				<input id="edit-dbhostname" type="text" name="dbhostname" placeholder="localhost"
				 value="{if $dbhostname}{$dbhostname|escape}{else}localhost{/if}" class="input-xlarge" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="edit-dbname">Database name</label>
			<div class="controls">
				<input id="edit-dbname" type="text" name="dbname" placeholder="dbname" value="{$dbname|escape}" class="input-large" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="edit-user">Database user</label>
			<div class="controls">
				<input id="edit-user" type="text" name="dbuser" placeholder="user" value="{$dbuser|escape}" class="input-large" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="edit-password">Database password</label>
			<div class="controls">
				<input id="edit-password" type="text" name="dbpassword" placeholder="password" value="{$dbpassword|escape}" class="input-large" />
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<input type="submit" class="btn btn-primary" value="Proceed to step 2" />
			</div>
		</div>
	</form>
</div>

{include file="inc.footer.tpl"}
