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
	<h2>Users <small>management</small></h2>
	<table class="table">
		<thead>
			<tr>
				<th>Name</th>
				<th>Email</th>
				<th>Admin</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$users item=listUser}
				<tr id="line-{$listUser.id}">
					<td>{$listUser.name|escape}</td>
					<td>{$listUser.email|escape}</td>
					<td>
						<input type="checkbox" value="1" id="check-admin-{$listUser.id}" {if $listUser.admin}checked="checked"{/if}
						 {if $listUser.id == $user.id}disabled="disabled"{/if}
						 {if $conf.demoMode}
							onchange="alert('[demo mode] This functionality is disabled')"
						 {else}
							onchange="adm.toggleAdmin({$listUser.id})"
						 {/if} />
						<img id="loading-{$listUser.id}" src="/img/loading.gif" class="hide" />
					</td>
					<td>
						{if $listUser.id != $user.id}
							<button class="btn btn-danger btn-mini"
							 {if $conf.demoMode}
								onclick="alert('[demo mode] This functionality is disabled')"
							 {else}
								onclick="adm.removeUser({$listUser.id})"
							 {/if}><i class="icon-trash"></i></button>
						{/if}
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	<div class="well">
		<form method="post" action="/admin/addUser" class="form-inline" {if $conf.demoMode}onsubmit="alert('[demo mode] This functionality is disabled'); return false"{/if}>
			<h4>Add user</h4>
			<div>
				<input type="text" name="name" placeholder="Name" autocomplete="off" />
				<input type="text" name="email" placeholder="Email" autocomplete="off" />
				<input id="edit-password" type="password" name="password" placeholder="Password" autocomplete="off" disabled="disabled" />
				<label class="checkbox"><input type="checkbox" name="admin" value="1" /> Admin</label>
				<input type="submit" class="btn btn-primary pull-right" value="create" />
			</div>
			<div style="margin-top: 0.6em;">
				<label class="checkbox">
					<input id="check-generate" type="checkbox" name="generate" value="1" checked="checked" onchange="adm.managePassword()"/>
					Generate password and send an email to the new user
				</label>
			</div>
		</form>
	</div>

	<h2>Database <small>download</small></h2>
	<form method="get" action="/admin/export" class="form-inline">
		<select name="format" onchange="if (this.value == 'html' && !$('#check-zip').is(':checked')) $('#check-zip').attr('checked', 'checked');">
			<option value="sql">SQL - full database</option>
			<option value="html">HTML only + ZIP</option>
		</select>
		<input type="submit" class="btn btn-primary" value="Download" />
	</form>
</div>

{include file="inc.footer.tpl"}
