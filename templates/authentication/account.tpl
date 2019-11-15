{include file="inc.header.tpl"}

<script>{literal}
var adm = new function() {
	/**
	 * Send to the server a request to change a user's admin status.
	 * @param	int	userId	User's identifier.
	 */
	this.toggleAdmin = function(userId) {
		$("#loading-" + userId).show();
		var checked = $("#check-admin-" + userId).is(":checked");
		var url = "/admin/setAdmin/" + userId + "/" + (checked ? "1" : "0");
		$.get(url, function(res) {
			$("#loading-" + userId).hide();
		});
	};
	/**
	 * Send to the server a request to remove a user.
	 * @param	int	userId	User's identifier.
	 */
	this.removeUser = function(userId) {
		if (!confirm("Remove this user?"))
			return;
		$("#loading-" + userId).show();
		$.get("/admin/removeUser/" + userId, function(res) {
			$("#loading-" + userId).hide();
			$("#line-" + userId).hide();
		});
	};
	/** Enable or disable the password field. */
	this.managePassword = function() {
		var checked = $("#check-generate").is(":checked");
		if (checked)
			$("#edit-password").attr("disabled", "disabled");
		else
			$("#edit-password").removeAttr("disabled");
	};
};
{/literal}</script>

<main class="app-main" style="font-size: 1rem;">
	<div class="container" style="padding-top: 2.4rem;">
		{if $paramsError}
			<div id="panel-error" class="card bg-danger p-3">
				Bad parameters.
			</div>
		{/if}
		{if !$editable}
			<div class="card bg-warning p-3">
				Demo mode: You can't modify this account.
			</div>
		{/if}
		{if $updated}
			<div class="card bg-success p-3">
				Your account has been updated.
			</div>
		{/if}
		<h2>My Account</h2>
		<form method="post" action="/authentication/updateAccount" class="form-horizontal">
			<div class="form-group">
				<label for="edit-name">Name</label>
				<input type="text" id="edit-name" class="form-control" name="name" placeholder="name" value="{$user.name|escape}" />
			</div>
			<div class="form-group">
				<label for="edit-email">Email</label>
				<input type="text" id="edit-email" name="email" class="form-control" placeholder="email" value="{$user.email|escape}"
				 {if !$editable}disabled="disabled" title="[demo mode] You can't change the email address"{/if} />
			</div>
			<div class="form-group">
				<label for="edit-pwd">Password</label>
				<input type="password" id="edit-pwd" name="password" class="form-control" placeholder="password"
				 {if !$editable}disabled="disabled" title="[demo mode] You can't change the password"{/if} />
			</div>
			<div class="form-group">
				<label for="edit-pwd2">Password (again)</label>
				<input type="password" id="edit-pwd2" name="password2" class="form-control" placeholder="password"
				 {if !$editable}disabled="disabled" title="[demo mode] You can't change the password"{/if} />
			</div>
			<div class="control-group">
				<div class="controls">
					<input type="submit" class="btn btn-primary" value="Update my account" {if !$editable}disabled{/if} />
				</div>
			</div>
		</form>
	</div>
</main>

{include file="inc.footer.tpl"}
