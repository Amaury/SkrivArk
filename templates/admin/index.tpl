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
		<div class="card" xstyle="background-color: #fff;">
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
						<input id="check-searchable" type="checkbox" name="searchable" value="1" class="custom-control-input"
						 {if $conf.searchable}checked="checked"{/if}>
						<label class="custom-control-label" for="check-searchable" style="padding-top: 0.05rem;">
							Searchable
							<small style="color: gray;">Contents are searchable</small>
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
					<div class="custom-control custom-switch" style="margin-bottom: 0.5rem;">
						<input id="check-darktheme" type="checkbox" name="darktheme" value="1" class="custom-control-input"
						 {if $conf.darkTheme}checked="checked"{/if}>
						<label class="custom-control-label" for="check-darktheme" style="padding-top: 0.05rem;">
							Dark theme
						</label>
					</div>
				</div>
				<div class="form-group">
					<label>Base font size</label>
					<select name="fontsize" class="form-control">
						<option value="">Default browser setting</option>
						<option value="12" {if $conf.fontsize == 12}selected{/if}>12 pixels</option>
						<option value="13" {if $conf.fontsize == 13}selected{/if}>13 pixels</option>
						<option value="14" {if $conf.fontsize == 14}selected{/if}>14 pixels</option>
						<option value="15" {if $conf.fontsize == 15}selected{/if}>15 pixels</option>
						<option value="16" {if $conf.fontsize == 16}selected{/if}>16 pixels</option>
						<option value="17" {if $conf.fontsize == 17}selected{/if}>17 pixels</option>
						<option value="18" {if $conf.fontsize == 18}selected{/if}>18 pixels</option>
						<option value="19" {if $conf.fontsize == 19}selected{/if}>19 pixels</option>
						<option value="20" {if $conf.fontsize == 20}selected{/if}>20 pixels</option>
						<option value="21" {if $conf.fontsize == 21}selected{/if}>21 pixels</option>
						<option value="22" {if $conf.fontsize == 22}selected{/if}>22 pixels</option>
						<option value="23" {if $conf.fontsize == 23}selected{/if}>23 pixels</option>
						<option value="24" {if $conf.fontsize == 24}selected{/if}>24 pixels</option>
						<option value="26" {if $conf.fontsize == 26}selected{/if}>26 pixels</option>
					</select>
					<small class="form-text text-muted">Set the whole interface zoom level</small>
				</div>
				<div class="row">
					<div class="col-12 col-lg-6">
						<div class="form-group">
							<label for="edit-fontname">Name of the text font</label>
							<input type="text" id="edit-fontname" name="fontname" value="{$conf.fontname|escape}" class="form-control" />
							<small class="form-text text-muted">Choose from the <a href="https://fonts.google.com/" target="_blank">Google Fonts</a> catalog, or leave empty to use the default font</small>
						</div>
						<div class="form-group">
							<label>Text font size</label>
							<select name="textsize" class="form-control">
								<option value="0.5" {if $conf.textsize == 0.5}selected{/if}>0.5</option>
								<option value="0.6" {if $conf.textsize == 0.6}selected{/if}>0.6</option>
								<option value="0.7" {if $conf.textsize == 0.7}selected{/if}>0.7</option>
								<option value="0.8" {if $conf.textsize == 0.8}selected{/if}>0.8</option>
								<option value="0.9" {if $conf.textsize == 0.9}selected{/if}>0.9</option>
								<option value="1" {if $conf.textsize == 1}selected{/if}>1</option>
								<option value="1.1" {if $conf.textsize == 1.1}selected{/if}>1.1</option>
								<option value="1.2" {if !$conf.textsize || $conf.textsize == 1.2}selected{/if}>1.2 (default size)</option>
								<option value="1.3" {if $conf.textsize == 1.3}selected{/if}>1.3</option>
								<option value="1.4" {if $conf.textsize == 1.4}selected{/if}>1.4</option>
								<option value="1.5" {if $conf.textsize == 1.5}selected{/if}>1.5</option>
								<option value="1.6" {if $conf.textsize == 1.6}selected{/if}>1.6</option>
								<option value="1.7" {if $conf.textsize == 1.7}selected{/if}>1.7</option>
								<option value="1.8" {if $conf.textsize == 1.8}selected{/if}>1.8</option>
								<option value="1.9" {if $conf.textsize == 1.9}selected{/if}>1.9</option>
								<option value="2" {if $conf.textsize == 2}selected{/if}>2</option>
								<option value="2.5" {if $conf.textsize == 2.5}selected{/if}>2.5</option>
								<option value="3" {if $conf.textsize == 3}selected{/if}>3</option>
								<option value="3.5" {if $conf.textsize == 3.5}selected{/if}>3.5</option>
								<option value="4" {if $conf.textsize == 4}selected{/if}>4</option>
								<option value="5" {if $conf.textsize == 5}selected{/if}>5</option>
							</select>
							<small class="form-text text-muted">Multiplier of the base font size</small>
						</div>
					</div>
					<div class="col-12 col-lg-6">
						<div class="form-group">
							<label for="edit-titlesfontname">Name of the titles font</label>
							<input type="text" id="edit-titlesfontname" name="titlesfontname" value="{$conf.titlesfontname|escape}" class="form-control" />
							<small class="form-text text-muted">Choose from the <a href="https://fonts.google.com/" target="_blank">Google Fonts</a> catalog, or leave empty to use the default font</small>
						</div>
						<div class="form-group">
							<label>Titles font size</label>
							<select name="titlessize" class="form-control">
								<option value="1.25" {if $conf.titlessize == 1.25}selected{/if}>h1=1.25 h2=0.80 h3=0.65 h4=0.45</option>
								<option value="1.5" {if $conf.titlessize == 1.5}selected{/if}>h1=1.5 h2=1 h3=0.75 h4=0.5</option>
								<option value="1.75" {if $conf.titlessize == 1.75}selected{/if}>h1=1.75 h2=1.25 h3=1 h4=0.75</option>
								<option value="2" {if $conf.titlessize == 2}selected{/if}>h1=2 h2=1.5 h3=1.25 h4=1</option>
								<option value="2.25" {if $conf.titlessize == 2.25}selected{/if}>h1=2.25 h2=1.75 h3=1.5 h4=1.25</option>
								<option value="2.5" {if !$conf.titlessize || $conf.titlessize == 2.5}selected{/if}>h1=2.5 h2=2 h3=1.75 h4=1.5 (default)</option>
								<option value="2.75" {if $conf.titlessize == 2.75}selected{/if}>h1=2.75 h2=2.25 h3=2 h4=1.75</option>
								<option value="3" {if $conf.titlessize == 3}selected{/if}>h1=3 h2=2.5 h3=2.25 h4=2</option>
								<option value="3.5" {if $conf.titlessize == 3.5}selected{/if}>h1=3.5 h2=3 h3=2.75 h4=2.25</option>
								<option value="4" {if $conf.titlessize == 4}selected{/if}>h1=4 h2=3.5 h3=3 h4=2.5</option>
								<option value="5" {if $conf.titlessize == 5}selected{/if}>h1=5 h2=4 h3=3.5 h4=3</option>
								<option value="6" {if $conf.titlessize == 6}selected{/if}>h1=6 h2=5 h3=4 h4=3.5</option>
								<option value="8" {if $conf.titlessize == 8}selected{/if}>h1=8 h2=6.5 h3=5 h4=4</option>
								<option value="10" {if $conf.titlessize == 10}selected{/if}>h1=10 h2=8 h3=6.5 h4=5</option>
							</select>
							<small class="form-text text-muted">Multiplier of the base font size</small>
						</div>
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
