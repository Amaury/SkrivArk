{include file="inc.header.tpl"}

<script type="text/javascript">{literal}<!--
var admin = new function() {
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
};
//-->{/literal}</script>

<div class="container">
	<h1>Users</h1>
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
						<input type="checkbox" value="1" id="check-admin-{$listUser.id}" {if $listUser.admin}checked="checked"{/if} {if $listUser.id == $user.id}disabled="disabled"{/if} onchange="admin.toggleAdmin({$listUser.id})" />
						<img id="loading-{$listUser.id}" src="/img/loading.gif" class="hide" />
					</td>
					<td>
						{if $listUser.id != $user.id}
							<button class="btn btn-danger btn-mini" onclick="admin.removeUser({$listUser.id})"><i class="icon-trash"></i></button>
						{/if}
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	<div class="well">
		<form method="post" action="/admin/addUser" class="form-inline">
			<h4>Add user</h4>
			<input type="text" name="name" placeholder="Name" autocomplete="off" />
			<input type="text" name="email" placeholder="Email" autocomplete="off" />
			<input type="password" name="password" placeholder="Password" autocomplete="off" />
			<label class="checkbox"><input type="checkbox" name="admin" value="1" /> Admin</label>
			<input type="submit" class="btn btn-primary pull-right" value="create" />
		</form>
	</div>
</div>

{include file="inc.footer.tpl"}
