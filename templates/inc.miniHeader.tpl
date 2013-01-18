<!DOCTYPE html>
<html lang="en">
<head>
	<title>SkrivArk{if $page}: {$page.title|escape}{/if}</title>
	{* bootstrap *}
	<link href="/css/bootstrap-2.2.2.min.css" rel="stylesheet" media="screen" />
	{* jQuery *}
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<script src="/js/jquery-ui-1.9.2.custom.min.js"></script>
	{* Google prettyprint *}
	<link href="/css/prettify.css" type="text/css" rel="stylesheet" />
	<script src="/js/prettify.js" type="text/javascript"></script>
	{* main style *}
	<link href="/css/style.css" rel="stylesheet" media="screen" />
</head>
<body onload="prettyPrint()" {if $coloredBackground}class="coloredBackground"{/if}>
