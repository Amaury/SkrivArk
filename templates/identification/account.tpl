{include file="inc.header.tpl"}

<script type="text/javascript">{literal}<!--
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
//-->{/literal}</script>

<div class="container">
	<h1>My Account</h1>
	<form method="post" action="/identification/updateAccount" class="form-horizontal">
		<div class="control-group">
			<label class="control-label" for="edit-name">Name</label>
			<div class="controls">
				<input type="text" id="edit-name" name="name" placeholder="name" value="{$user.name|escape}" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="edit-email">Email</label>
			<div class="controls">
				<input type="text" id="edit-email" name="email" placeholder="email" value="{$user.email|escape}" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="edit-pwd">Password</label>
			<div class="controls">
				<input type="password" id="edit-pwd" name="password" placeholder="password" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="edit-pwd2">Password (again)</label>
			<div class="controls">
				<input type="password" id="edit-pwd2" name="password2" placeholder="password" />
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<input type="submit" class="btn btn-primary" value="Update my account" />
			</div>
		</div>
	</form>
</div>

{include file="inc.footer.tpl"}
