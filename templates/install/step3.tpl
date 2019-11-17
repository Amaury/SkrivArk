{include file="inc.header.tpl"}

<script>{literal}
	function checkForm() {
		var ok = true;
		if (!$("#edit-sitename").val().length) {
			ok = false;
			$("#edit-sitename").parent().parent().addClass("error");
		}
		if (!$("#edit-baseurl").val().length) {
			ok = false;
			$("#edit-baseurl").parent().parent().addClass("error");
		}
		if (!$("#edit-emailsender").val().length) {
			ok = false;
			$("#edit-emailsender").parent().parent().addClass("error");
		}
		return (ok);
	}
{/literal}</script>

<main class="app-main" style="font-size: 1rem;">
	<div class="container" style="padding-top: 2.4rem;">
		<h2 style="margin-top: 40px;">Step 3 <small>Site parameters</small></h2>
		{if $paramerror}
			<div id="panel-error" class="card bg-danger p-3">
				Please check your parameters.
			</div>
		{/if}
		<form method="post" action="/install/proceedStep3" onsubmit="return checkForm()" onkeypress="$('.form-group').removeClass('error'); $('#panel-error').hide()">
			<div class="form-group">
				<label for="edit-sitename">Site name</label>
				<input type="text" id="edit-sitename" name="sitename"
				 value="{$sitename|escape}" placeholder="Name of your site" class="form-control" />
			</div>
			<div class="form-group">
				<label for="edit-baseurl">Base URL</label>
				<input type="text" id="edit-baseurl" name="baseurl" placeholder="http://yoursite.com"
				 value="{if $baseurl}{$baseurl|escape}{else}{if $smarty.server.HTTPS && $smarty.server.HTTPS != 'off'}https{else}http{/if}://{$smarty.server.HTTP_HOST}{/if}"
				 class="form-control" />
				<small class="form-text text-muted">No trailing slash</small>
			</div>
			<div class="form-group">
				<label for="edit-emailsender">Email sender</label>
				<input type="text" id="edit-emailsender" name="emailsender"
				 value="{$emailsender|escape}" placeholder="Contact <contact@domain.com>" class="form-control" />
			</div>
			<div class="form-group">
				<label>Options</label>
				<div class="form-check">
					<input id="check-demomode" class="form-check-input" type="checkbox" name="demomode" value="1" {if $demomode}checked="checked"{/if}>
					<label class="form-check-label" for="check-demomode">
						Demo mode
					</label>
				</div>
				<div class="form-check">
					<input id="check-ro" class="form-check-input" type="checkbox" name="allowreadonly" value="1" {if $allowreadonly !== false}checked="checked"{/if}>
					<label class="form-check-label" for="check-ro">
						Allow read-only access
						<small>Pages are readable even for non-logged visitors</small>
					</label>
				</div>
			</div>
			<div class="form-group">
				<label for="edit-disqus">Disqus ID</label>
				<input type="text" id="edit-disqus" name="disqus" class="form-control" value="{$disqus|escape}" />
				<small class="form-text text-muted"><a href="http://www.disqus.com/websites/" target="_blank" title="Disqus.com">Create your account</a> to add comments on your pages</small>
			</div>
			<div class="form-group">
				<label for="edit-googleanalytics">Google Analytics ID</label>
				<input type="text" id="edit-googleanalytics" name="googleanalytics" class="form-control" value="{$googleanalytics|escape}" />
				<small class="form-text text-muted"><a href="http://www.google.com/analytics/" target="_blank" title="Google Analytics">Create your account</a> to get audience statistics</small>
			</div>
			<div class="form-group">
				<label for="select-loglevel">Log level</label>
				<select name="loglevel" class="custom-select">
					<option value="ERROR" {if $loglevel == 'ERROR'}selected="selected"{/if}>ERROR</option>
					<option value="WARN" {if $loglevel == 'WARN' || !$loglevel}selected="selected"{/if}>WARN</option>
					<option value="NOTE" {if $loglevel == 'NOTE'}selected="selected"{/if}>NOTE</option>
					<option value="INFO" {if $loglevel == 'INFO'}selected="selected"{/if}>INFO</option>
					<option value="DEBUG" {if $loglevel == 'DEBUG'}selected="selected"{/if}>DEBUG</option>
				</select>
			</div>
			<div class="form-group">
				<input type="submit" class="btn btn-primary" value="Proceed to step 4" />
			</div>
		</form>
	</div>
</main>

{include file="inc.footer.tpl"}
