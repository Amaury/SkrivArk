<!DOCTYPE html>
<html lang="en">
<head>
	<title>{if $conf.sitename}{$conf.sitename|escape}{else}SkrivArk{/if}{if $page}: {$page.title|escape}{/if}</title>
	<link rel="icon" type="image/png" href="/favicon.png" />
	{* jQuery *}
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<script src="/js/jquery-ui-1.9.2.custom.min.js"></script>
	{* bootstrap *}
	<link href="/css/bootstrap-2.3.1.css" rel="stylesheet" media="screen" />
	<script src="/js/bootstrap.min.js" type="text/javascript"></script>
	{* Google prettyprint *}
	<link href="/css/prettify.css" type="text/css" rel="stylesheet" />
	<script src="/js/prettify.js" type="text/javascript"></script>
	{* main *}
	<link href="/css/style.css" rel="stylesheet" media="screen" />
	<script src="/js/ark.js" type="text/javascript"></script>
	{* Disqus *}
	{if $conf.disqus}
		<script type="text/javascript" src="//{$conf.disqus}.disqus.com/embed.js" async></script>
	{/if}
</head>
<body onload="prettyPrint()" {if $coloredBackground}class="coloredBackground"{/if}>
