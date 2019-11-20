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
							<img id="loading-{$listUser.id}" src="/img/loading.gif" style="display: none;" />
						</td>
						<td>
							{if $listUser.id != $user.id}
								<button class="btn btn-danger btn-mini" title="Remove this user"
								 {if $conf.demoMode}
									onclick="alert('[demo mode] This functionality is disabled')"
								 {else}
									onclick="adm.removeUser({$listUser.id})"
								 {/if}><i class="fas fa-trash"></i></button>
							{/if}
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
		<div class="card" style="background-color: #fff;">
			<div class="card-header">
				<h4>Add user</h4>
			</div>
			<div class="card-body">
				<form method="post" action="/admin/addUser" class="form-inline" {if $conf.demoMode}onsubmit="alert('[demo mode] This functionality is disabled'); return false"{/if}>
					<table style="width: 100%;"><tr>
						<td><input type="text" class="form-control" name="name" placeholder="Name" autocomplete="off" /></td>
						<td><input type="text" class="form-control" name="email" placeholder="Email" autocomplete="off" /></td>
						<td><input id="edit-password" type="password" class="form-control" name="password" placeholder="Password" autocomplete="off" disabled="disabled" /></td>
						<td><label class="checkbox"><input type="checkbox" name="admin" value="1" /> Admin</label></td>
						<td><input type="submit" class="btn btn-primary pull-right" value="create" /></td>
					</tr></table>
					<div style="margin-top: 0.6em;">
						<label class="checkbox">
							<input id="check-generate" type="checkbox" name="generate" value="1" checked="checked" onchange="adm.managePassword()"/>
							Generate password and send an email to the new user
						</label>
					</div>
				</form>
			</div>
		</div>

		<h2 style="margin-top: 40px;">Configuration</h2>
		{if !$editableConfig}
			<p>
				Add writing rights to the <tt>etc/temma.json</tt> file to be able to edit it online.
			</p>
		{else}
			<p id="link-config">
				<a href="#" onclick="$('#link-config').hide(); $('#form-config').slideToggle('slow'); return false">Edit configuration parameters</a>
			</p>
			<form id="form-config" method="post" action="/admin/config" style="display: none;">
				<div class="form-group">
					<label>Database DSN</label>
					<input type="text" value="{$dbDSN|escape}" disabled="disabled" class="form-control uneditable-input" />
				</div>
				<div class="form-group">
					<label>Cache DSN</label>
					<input type="text" value="{$cacheDSN|escape}" disabled="disabled" class="form-control uneditable-input" />
				</div>
				<div class="form-group">
					<label for="edit-sitename">Site name</label>
					<input type="text" id="edit-sitename" name="sitename" value="{$conf.sitename|escape}" class="form-control" />
				</div>
				<div class="form-group">
					<label for="edit-baseurl">Base URL</label>
					<input type="text" id="edit-baseurl" name="baseurl" value="{$conf.baseURL|escape}" class="form-control" />
					<small class="form-text text-muted">No trailing slash</small>
				</div>
				<div class="form-group">
					<label for="edit-emailsender">Email sender</label>
					<input type="text" id="edit-emailsender" name="emailsender" value="{$conf.emailSender|escape}" class="form-control" />
				</div>
				<div class="form-group">
					<label>Options</label>
					<div class="custom-control custom-switch" style="margin-bottom: 0.5rem;">
						<input id="check-demo" type="checkbox" name="demomode" value="1" class="custom-control-input"
						 {if $conf.demoMode}checked="checked"{/if}>
						<label class="custom-control-label" for="check-demo" style="padding-top: 0.05rem;">
							Demo mode
						</label>
					</div>
					<div class="custom-control custom-switch" style="margin-bottom: 0.5rem;">
						<input id="check-ro" type="checkbox" name="allowreadonly" value="1" class="custom-control-input"
						 {if $conf.allowReadOnly}checked="checked"{/if} onchange="$('#check-private').prop('disabled', !this.checked)">
						<label class="custom-control-label" for="check-ro" style="padding-top: 0.05rem;">
							Allow read-only
							<small style="color: gray;">Pages are readable even for non-logged visitors</small>
						</label>
					</div>
					<div class="custom-control custom-switch" style="margin-bottom: 0.5rem;">
						<input id="check-private" type="checkbox" name="allowprivatepages" value="1" class="custom-control-input"
						 {if $conf.allowPrivatePages}checked="checked"{/if} {if !$conf.allowReadOnly}disabled{/if}>
						<label class="custom-control-label" for="check-private" style="padding-top: 0.05rem;">
							Allow private pages
							<small style="color: gray;">Private pages are visible only for logged users</small>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label for="edit-disqus">Disqus ID</label>
					<input type="text" id="edit-disqus" name="disqus" value="{$conf.disqus|escape}" class="form-control" />
					<small class="form-text text-muted"><a href="http://www.disqus.com/websites/" target="_blank" title="Disqus.com">Create your account</a> to add comments on your pages</small>
				</div>
				<div class="form-group">
					<label for="edit-googleanalytics">Google Analytics ID</label>
					<input type="text" id="edit-googleanalytics" name="googleanalytics" value="{$conf.googleAnalytics|escape}" class="form-control" />
					<small class="form-text text-muted"><a href="http://www.google.com/analytics/" target="_blank" title="Google Analytics">Create your account</a> to get audience statistics</small>
				</div>
				<div class="form-group">
					<label for="select-loglevel">Log level</label>
					<select name="loglevel" class="custom-select">
						<option value="ERROR" {if $logLevel == 'ERROR'}selected="selected"{/if}>ERROR</option>
						<option value="WARN" {if $logLevel == 'WARN'}selected="selected"{/if}>WARN</option>
						<option value="INFO" {if $logLevel == 'INFO'}selected="selected"{/if}>INFO</option>
						<option value="NOTE" {if $logLevel == 'NOTE'}selected="selected"{/if}>NOTE</option>
						<option value="DEBUG" {if $logLevel == 'DEBUG'}selected="selected"{/if}>DEBUG</option>
					</select>
				</div>
				<div class="form-group">
					<div class="controls">
						<input type="submit" class="btn btn-primary" value="Update" />
					</div>
				</div>
			</form>
		{/if}

		<h2 style="margin-top: 40px;">Splashscreen</h2>
		{if !$editableSplashscreen}
			<p>
				Add writing rights to the <tt>var/splashscreen.html</tt> file to be able to edit it online.
			</p>
		{else}
			<p id="link-splashscreen">
				<a href="#" onclick="$('#link-splashscreen').hide(); $('#form-splashscreen').slideToggle('slow'); return false">Edit splashscreen</a>
			</p>
			<form id="form-splashscreen" method="post" action="/admin/splash" style="display: none;" onsubmit="return checkHtmlForm()">
				<textarea id="splashhtml" name="html" class="form-control" style="width: 100%; height: 10em;" onkeydown="$('#splasherror').hide()">{$splashscreen}</textarea>
				<div id="splasherror" class="invalid-feedback" style="display: none;">Invalid HTML Code</div>
				<input type="submit" class="btn btn-primary" value="Save" />
			</form>
		{/if}

		<h2 style="margin-top: 40px;">Database <small>download</small></h2>
		<form method="get" action="/admin/export" class="form-inline">
			<select name="format" class="custom-select" style="margin-right: 0.4rem;"
			 onchange="if (this.value == 'html' && !$('#check-zip').is(':checked')) $('#check-zip').attr('checked', 'checked');">
				<option value="sql">SQL - full database</option>
				<option value="html">zipped HTML - content only</option>
			</select>
			<input type="submit" class="btn btn-primary" value="Download" />
		</form>
	</div>
</main>

<script>{literal}
	var htmlStatus = false;
	function checkHtmlForm() {
		if (htmlStatus)
			return (true);
		var data = {
			html: $("#splashhtml").val()
		};
		$.post("/admin/checkHtml", data, function(result) {
			if (!result) {
				$("#splasherror").show();
				return;
			}
			htmlStatus = true;
			$("#form-splashscreen").submit();
		}, "json");
		return (false);
	}
{/literal}</script>

{include file="inc.footer.tpl"}
