{include file="inc.header.tpl"}

<script type="text/javascript">{literal}<!--
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
//-->{/literal}</script>

<div class="container">
	<h2 style="margin-top: 40px;">Step 4 <small>Default admnistrator user</small></h2>
	{if $adminerror}
		<div id="panel-error" class="alert alert-error">
			Please check your parameters.
		</div>
	{/if}
	<form method="post" action="/install/proceedStep4" onsubmit="return checkForm()" onkeypress="$('.control-group').removeClass('error'); $('#panel-error').hide()" class="form-horizontal">
		<div class="control-group">
			<label class="control-label" for="edit-adminname">Name</label>
			<div class="controls"><input type="text" id="edit-adminname" name="adminname" placeholder="Your name"
			 value="{$adminname|escape}" class="input-xlarge" /></div>
		</div>
		<div class="control-group">
			<label class="control-label" for="edit-adminemail">Email address</label>
			<div class="controls"><input type="text" id="edit-adminemail" name="adminemail" placeholder="user@domain.com"
			 value="{$adminemail|escape}" class="input-xlarge" /></div>
		</div>
		<div class="control-group">
			<label class="control-label" for="edit-adminpassword">Password</label>
			<div class="controls"><input type="password" id="edit-adminpassword" name="adminpassword" value="" placeholder="password" class="input-large" /></div>
		</div>
		<div class="control-group">
			<div class="controls">
				<input type="submit" class="btn btn-primary" value="Create configuration" />
			</div>
		</div>
	</form>
</div>

{include file="inc.footer.tpl"}
