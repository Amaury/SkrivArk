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

<div class="container">
	<h2 style="margin-top: 40px;">Step 3 <small>Site parameters</small></h2>
	{if $paramerror}
		<div id="panel-error" class="alert alert-error">
			Please check your parameters.
		</div>
	{/if}
	<form method="post" action="/install/proceedStep3" onsubmit="return checkForm()" onkeypress="$('.control-group').removeClass('error'); $('#panel-error').hide()" class="form-horizontal">
		<div class="control-group">
			<label class="control-label" for="edit-sitename">Site name</label>
			<div class="controls"><input type="text" id="edit-sitename" name="sitename"
			 value="{$sitename|escape}" placeholder="Name of your site" class="input-xlarge" /></div>
		</div>
		<div class="control-group">
			<label class="control-label" for="edit-baseurl">Base URL</label>
			<div class="controls">
				<input type="text" id="edit-baseurl" name="baseurl" placeholder="http://yoursite.com"
				 value="{if $baseurl}{$baseurl|escape}{else}http://{$smarty.server.HTTP_HOST}{/if}" class="input-xxlarge" />
				<span class="help-inline">No trailing slash</span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="edit-emailsender">Email sender</label>
			<div class="controls"><input type="text" id="edit-emailsender" name="emailsender"
			 value="{$emailsender|escape}" placeholder="Contact <contact@domain.com>" class="input-xxlarge" /></div>
		</div>
		<div class="control-group">
			<div class="controls">
				<label class="checkbox">
					<input type="checkbox" name="demomode" value="1" {if $demomode}checked="chcked"{/if} /> Demo mode
				</label>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<label class="checkbox">
					<input type="checkbox" name="titledurl" value="1" {if $titledurl !== false}checked="checked"{/if} /> Titled URL
				</label>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<label class="checkbox">
					<input type="checkbox" name="allowreadonly" value="1" {if $allowreadonly}checked="checked"{/if}/> Allow read-only access
				</label>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="edit-disqus">Disqus ID</label>
			<div class="controls">
				<input type="text" id="edit-disqus" name="disqus" class="input-xlarge" value="{$disqus|escape}" />
				<span class="help-inline"><a href="http://www.disqus.com/websites/" target="_blank" title="Disqus.com">Create your account</a> to add comments on your pages</span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="edit-googleanalytics">Google Analytics ID</label>
			<div class="controls">
				<input type="text" id="edit-googleanalytics" name="googleanalytics" class="input-xlarge" value="{$googleanalytics|escape}" />
				<span class="help-inline"><a href="http://www.google.com/analytics/" target="_blank" title="Google Analytics">Create your account</a> to get audience statistics</span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="select-loglevel">Log level</label>
			<div class="controls">
				<select name="loglevel" class="input-small">
					<option value="ERROR" {if $loglevel == 'ERROR'}selected="selected"{/if}>ERROR</option>
					<option value="WARN" {if $loglevel == 'WARN' || !$loglevel}selected="selected"{/if}>WARN</option>
					<option value="NOTE" {if $loglevel == 'NOTE'}selected="selected"{/if}>NOTE</option>
					<option value="INFO" {if $loglevel == 'INFO'}selected="selected"{/if}>INFO</option>
					<option value="DEBUG" {if $loglevel == 'DEBUG'}selected="selected"{/if}>DEBUG</option>
				</select>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<input type="submit" class="btn btn-primary" value="Proceed to step 4" />
			</div>
		</div>
	</form>
</div>

{include file="inc.footer.tpl"}
