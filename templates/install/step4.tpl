{include file="inc.header.tpl"}

<script>{literal}
	function checkForm() {
		var ok = true;
		if (!$("#edit-adminname").val().length) {
			ok = false;
			$("#edit-adminname").parent().parent().addClass("error");
		}
		if (!$("#edit-adminemail").val().length) {
			ok = false;
			$("#edit-adminemail").parent().parent().addClass("error");
		}
		if ($("#edit-adminpassword").val().length < 6) {
			ok = false;
			$("#edit-adminpassword").parent().parent().addClass("error");
		}
		return (ok);
	}
{/literal}</script>

<main class="app-main" style="font-size: 1rem;">
	<div class="container" style="padding-top: 2.4rem;">
		<h2 style="margin-top: 40px;">Step 4 <small>Default admnistrator user</small></h2>
		{if $adminerror}
			<div id="panel-error" class="card bg-danger p-3">
				Please check your parameters.
			</div>
		{/if}
		<form id="form-step4" method="post" action="/install/proceedStep4" onsubmit="return checkForm()" onkeypress="$('.form-group').removeClass('error'); $('#panel-error').hide()">
			<div class="form-group">
				<label for="edit-adminname">Name</label>
				<input type="text" id="edit-adminname" name="adminname" placeholder="Your name"
				 value="{$adminname|escape}" class="form-control" />
			</div>
			<div class="form-group">
				<label for="edit-adminemail">Email address</label>
				<input type="text" id="edit-adminemail" name="adminemail" placeholder="user@domain.com"
				 value="{$adminemail|escape}" class="form-control" />
			</div>
			<div class="form-group">
				<label for="edit-adminpassword">Password</label>
				<input type="password" id="edit-adminpassword" name="adminpassword" value="" placeholder="password" class="form-control" />
			</div>
			<div class="form-group">
				<input type="submit" class="btn btn-primary" value="Create user" />
			</div>
		</form>
	</div>
</main>

{include file="inc.footer.tpl"}
