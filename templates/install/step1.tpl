{include file="inc.header.tpl"}

<script>{literal}
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
{/literal}</script>

<main class="app-main" style="font-size: 1rem;">
	<div class="container" style="padding-top: 2.4rem;">
		<h2 style="margin-top: 40px;">Step 1 <small>MySQL/MariaDB Database</small></h2>
		{if $dberror}
			<div id="panel-error" class="card bg-danger p-3">
				Unable to connect to the database. Please check your parameters.<br />
				Maybe you need to create your database and user first:
				<div style="padding: 0.4rem; background-color: rgba(255, 255, 255, 0.4);"><tt>CREATE DATABASE <strong>{$dbname|escape}</strong> DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE utf8mb4_general_ci;<br />
CREATE USER '<strong>{$dbuser|escape}</strong>'@'<strong>{$dbhostname|escape}</strong>' IDENTIFIED WITH mysql_native_password BY '<strong>{$dbpassword|escape}</strong>';<br />
GRANT ALL PRIVILEGES ON <strong>{$dbname|escape}</strong>.* to '<strong>{$dbuser|escape}</strong>'@'<strong>{$dbhost|escape}</strong>';</tt></div>
			</div>
		{/if}
		<form method="post" action="/install/proceedStep1" onsubmit="return checkForm()" onkeypress="$('.form-group').removeClass('error'); $('#panel-error').hide()">
			<div class="form-group">
				<label for="edit-dbhostname">Hostname</label>
				<input id="edit-dbhostname" type="text" name="dbhostname" placeholder="localhost"
				 value="{if $dbhostname}{$dbhostname|escape}{else}localhost{/if}" class="form-control" />
			</div>
			<div class="form-group">
				<label for="edit-dbname">Database name</label>
				<input id="edit-dbname" type="text" name="dbname" placeholder="dbname" value="{$dbname|escape}" class="form-control" />
			</div>
			<div class="form-group">
				<label for="edit-user">Database user</label>
				<input id="edit-user" type="text" name="dbuser" placeholder="user" value="{$dbuser|escape}" class="form-control" />
			</div>
			<div class="form-group">
				<label for="edit-password">Database password</label>
				<input id="edit-password" type="text" name="dbpassword" placeholder="password" value="{$dbpassword|escape}" class="form-control" />
			</div>
			<div class="form-group">
				<div id="panel-error" class="card bg-warning p-3">
					Warning! Your database will be deleted and re-created. Any existing data will be lost!
				</div>
			</div>
			<div class="form-group">
				<input type="submit" class="btn btn-primary" value="Proceed to step 2" />
			</div>
		</form>
	</div>
</main>

{include file="inc.footer.tpl"}
