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

	<h2 style="margin-top: 40px;">Configuration</h2>
	{if !$editableConfig}
		<p>
			Add writing rights to the <tt>etc/temma.json</tt> file to be able to edit it online.
		</p>
	{else}
		<p id="link-config">
			<a href="#" onclick="$('#link-config').hide(); $('#form-config').slideToggle('slow'); return false">Edit configuration parameters</a>
		</p>
		<form id="form-config" method="post" action="/admin/config" class="form-horizontal hide">
			<div class="control-group">
				<label class="control-label">Database DSN</label>
				<div class="controls"><input type="text" value="{$dbDSN|escape}" disabled="disabled" class="uneditable-input input-xxlarge" /></div>
			</div>
			<div class="control-group">
				<label class="control-label">Cache DSN</label>
				<div class="controls"><input type="text" value="{$cacheDSN|escape}" disabled="disabled" class="uneditable-input input-xxlarge" /></div>
			</div>
			<div class="control-group">
				<label class="control-label" for="edit-sitename">Site name</label>
				<div class="controls"><input type="text" id="edit-sitename" name="sitename" value="{$conf.sitename|escape}" class="input-xlarge" /></div>
			</div>
			<div class="control-group">
				<label class="control-label" for="edit-baseurl">Base URL</label>
				<div class="controls">
					<input type="text" id="edit-baseurl" name="baseurl" value="{$conf.baseURL|escape}" class="input-xxlarge" />
					<span class="help-inline">No trailing slash</span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="edit-emailsender">Email sender</label>
				<div class="controls"><input type="text" id="edit-emailsender" name="emailsender" value="{$conf.emailSender|escape}" class="input-xxlarge" /></div>
			</div>
			<div class="control-group">
				<div class="controls">
					<label class="checkbox">
						<input type="checkbox" name="demomode" value="1" {if $conf.demoMode}checked="checked"{/if} /> Demo mode
					</label>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<label class="checkbox">
						<input type="checkbox" name="titledurl" value="1" {if $conf.titledURL}checked="checked"{/if} /> Titled URL
					</label>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<label class="checkbox">
						<input type="checkbox" name="allowreadonly" value="1" {if $conf.allowReadOnly}checked="checked"{/if} /> Allow read-only
					</label>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="edit-disqus">Disqus ID</label>
				<div class="controls">
					<input type="text" id="edit-disqus" name="disqus" value="{$conf.disqus|escape}" class="input-xlarge" />
					<span class="help-inline"><a href="http://www.disqus.com/websites/" target="_blank" title="Disqus.com">Create your account</a> to add comments on your pages</span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="edit-googleanalytics">Google Analytics ID</label>
				<div class="controls">
					<input type="text" id="edit-googleanalytics" name="googleanalytics" value="{$conf.googleAnalytics|escape}" class="input-xlarge" />
					<span class="help-inline"><a href="http://www.google.com/analytics/" target="_blank" title="Google Analytics">Create your account</a> to get audience statistics</span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="select-loglevel">Log level</label>
				<div class="controls">
					<select name="loglevel" class="input-small">
						<option value="ERROR" {if $logLevel == 'ERROR'}selected="selected"{/if}>ERROR</option>
						<option value="WARN" {if $logLevel == 'WARN'}selected="selected"{/if}>WARN</option>
						<option value="INFO" {if $logLevel == 'INFO'}selected="selected"{/if}>INFO</option>
						<option value="NOTE" {if $logLevel == 'NOTE'}selected="selected"{/if}>NOTE</option>
						<option value="DEBUG" {if $logLevel == 'DEBUG'}selected="selected"{/if}>DEBUG</option>
					</select>
				</div>
			</div>
			<div class="control-group">
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
		<form id="form-splashscreen" method="post" action="/admin/splash" class="hide" onsubmit="return checkHtmlForm()">
			<textarea id="splashhtml" name="html" style="width: 100%; height: 10em;" onkeydown="$('#splasherror').hide()">{$splashscreen}</textarea>
			<div id="splasherror" class="control-group error hide"><span class="help-inline">Invalid HTML Code</span></div>
			<input type="submit" class="btn btn-primary" value="Save" />
		</form>
	{/if}

	<h2 style="margin-top: 40px;">Database <small>download</small></h2>
	<form method="get" action="/admin/export" class="form-inline">
		<select name="format" onchange="if (this.value == 'html' && !$('#check-zip').is(':checked')) $('#check-zip').attr('checked', 'checked');">
			<option value="sql">SQL - full database</option>
			<option value="html">HTML only + ZIP</option>
		</select>
		<input type="submit" class="btn btn-primary" value="Download" />
	</form>
</div>

<script type="text/javascript">{literal}
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
