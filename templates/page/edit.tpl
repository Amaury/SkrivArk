<!DOCTYPE html>
<html lang="en">
<head>
	<title>{if $conf.title}{$conf.title|escape}{else}SkrivArk{/if}{if $page}: {$page.title|escape}{/if}</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	{* bootstrap *}
	<link href="/css/bootstrap-2.2.2.min.css" rel="stylesheet" media="screen" />
	<script src="/js/bootstrap.min.js" type="text/javascript"></script>
	{if $prettyprint}
		{* Google prettyprint *}
		<link href="/css/prettify.css" type="text/css" rel="stylesheet" />
		<script src="/js/prettify.js" type="text/javascript"></script>
	{/if}
	{*<link href="/css/style.css" rel="stylesheet" media="screen" />*}
	<style type="text/css">{literal}
		html, body {
			background-color: #eaeaea;
			margin: 0;
			border: 0;
			position: absolute;
			left: 0;
			right: 0;
			top: 0;
			bottom: 0;
			height: 100%;
		}
		div#breadcrumb {
			position: absolute;
			top: 50px;
		}
		div#title {
			position: absolute;
			top: 96px;
		}
		div#body-content {
			position: absolute;
			top: 118px;
			bottom: 5px;
			left: 0;
			right: 0;
		}
		/* textarea */
		textarea#skrivtext {
			position: absolute;
			font-family: monospace;
			margin: 1%;
			width: 48%;
			left: 0;
			top: 0;
			bottom: 0;
			color: #000;
		}
		/* content */
		div#skrivhtml {
			position: absolute;
			overflow: auto;
			margin: 1%;
			width: 46%;
			right: 0;
			top: 0;
			bottom: 0;
			border: 1px solid #000;
			padding: 1%;
			background-color: #fff;
		}
		div#skrivhtml h1 {
			margin-top: 1em;
		}
		/* table */
		div#skrivhtml table.bordered {
			border: 1px solid #888;
		}
		div#skrivhtml table.bordered th {
			border: 1px solid #888;
			padding: 3px;
			background-color: #eee;
		}
		div#skrivhtml table.bordered td {
			border: 1px solid #888;
			padding: 3px;
		}
		div.bordered {
			border: 1px solid #888;
			display: inline-block;
		}
		/* footnotes */
		div#skrivhtml div.footnotes {
			margin-top: 2em;
			border-top: 1px dashed #aaa;
			padding-top: 1em;
		}
		div#skrivhtml div.footnotes p.footnote {
			margin: 0;
			font-size: 0.9em;
		}
	{/literal}</style>
</head>
<body {if $prettyprint}onload="prettyPrint()"{/if}>

{* modal dialog window for SkrivML syntax cheat sheet *}
<div id="popup-syntax" class="modal hide fade" role="dialog" style="width: 750px;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3>SkrivML syntax cheat sheet</h3>
	</div>
	<div class="modal-body"></div>
</div>

{* header *}
<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container-fluid">
			<a class="brand" href="/">{if $conf.sitename}{$conf.sitename|escape}{else}SkrivArk{/if}</a>
			<ul class="nav">
				{if $user.admin}
					<li {if $CONTROLLER == "admin"}class="active"{/if}><a href="/admin">Admin</a></li>
				{/if}
			</ul>
			<ul class="nav pull-right">
				<li><a href="/identification/account">My account</a></li>
				<li><a href="/identification/logout">Logout</a></li>
			</ul>
		</div>
	</div>
</div>

{* breadcrumbs *}
<div id="breadcrumb" class="container-fluid">
	<ul class="breadcrumb">
		<li><a href="/" title="Back to home page"><i class="icon-home"></i></a>{if $breadcrumb || $page} <span class="divider">/</span>{/if}</li>
		{foreach name=breadcrumb from=$breadcrumb item=crumb}
			<li>
				<a href="/page/show/{$crumb.id}">{$crumb.title|escape}</a>
				<span class="divider">/</span>
			</li>
		{/foreach}
		{if $page}
			<li title="{if $page.nbrVersions > 1}Last edition by {$page.modifierName|escape} on {$page.modifDate}{else}Created by {$page.creatorName|escape} on {$page.creationDate}{/if}"><a href="/page/show/{$page.id}"><strong>{$page.title|escape}</strong></a></li>
		{/if}
	</ul>
</div>

{* main body *}
<form id="form" method="post"
 {if $page}
	action="/page/storeEdit/{$page.id}"
 {else}
	action="/page/storeCreate/{$parentId}"
 {/if}>
	<div style="position: absolute; top: 96px; left: 0; right: 0;">
		<div class="container-fluid">
			<input id="edit-title" type="text" name="title" value="{$page.title|escape}" placeholder="Title" autocomplete="off" style="width: 48%;" />
			<input type="submit" class="btn btn-primary" value="{if $ACTION == "create"}Create the page{else}Save modifications{/if}" style="margin: -10px 0 0 10px;" />
			<a href="/page/show/{if $page}{$page.id}{else}{$parentId}{/if}" class="btn" style="margin: -10px 0 0 5px;">Cancel</a>
			<a href="http://markup.skriv.org/language/cheatSheet?blank=1" data-toggle="modal" data-target="#popup-syntax"class="pull-right">SkrivML syntax cheat sheet</a>
		</div>
	</div>

	<div id="body-content">
		<textarea id="skrivtext" name="content">{if $editContent}{$editContent}{else}{$page.skriv}{/if}</textarea>
		<div id="skrivhtml">{$page.html}</div>
	</div>
</form>

<script type="text/javascript">{literal}<!--
	var timer = null;
	// put focus on input zone
	if (!$("#edit-title").val().length)
		$("#edit-title").focus();
	else
		$("#skrivtext").focus();
	// text modification event
	$("#skrivtext").bind("input propertychange", function() {
		if (timer)
			clearTimeout(timer);
		timer = setTimeout(function() {
			var text = $("#skrivtext").val();
			var url = "/page/convert";
			$("#skrivhtml").load(url, {text: text});
		}, 300);
	});
//-->{/literal}</script>

</body>
</html>
