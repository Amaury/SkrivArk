<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"><!-- End Required meta tags -->
	<title>{if $conf.sitename}{$conf.sitename|escape}{else}SkrivArk{/if}{if $page}: {$page.title|escape}{/if}</title>
	<meta property="og:title" content="{if $conf.sitename}{$conf.sitename|escape}{else}SkrivArk{/if}{if $page}: {$page.title|escape}{/if}">
	{if $conf.sitename}
		<meta property="og:site_name" content="{$conf.sitename|escape}">
	{/if}
	{* jQuery *}
	<script src="/js/jquery.min.js"></script>
	{* Google font *}
	<link href="https://fonts.googleapis.com/css?family=Fira+Sans:400,500,600" rel="stylesheet"><!-- End Google font -->
	{* Favicons *}
	<link rel="icon" type="image/png" href="/favicon.png" />
	<link rel="shortcut icon" href="/favicon.ico">
	<meta name="theme-color" content="#070B0F">
	{* BEGIN PLUGINS STYLES *}
	<link rel="stylesheet" href="/css/open-iconic-bootstrap.min.css">
	<link rel="stylesheet" href="/vendors/fontawesome/css/all.css">
	{* BEGIN THEME STYLES *}
	<link rel="stylesheet" href="/css/theme.min.css" data-skin="default">
	<link rel="stylesheet" href="/css/theme-dark.min.css" data-skin="dark"><!-- Disable unused skin immediately -->
	<script>
		var skin = localStorage.getItem('skin') || 'default';
		var unusedLink = document.querySelector('link[data-skin]:not([data-skin="' + skin + '"])');
		unusedLink.setAttribute('rel', '');
		unusedLink.setAttribute('disabled', true);
	</script>
	{* main *}
	<link href="/css/style.css" rel="stylesheet" media="screen" />
	<script src="/js/ark.js"></script>
	{* Disqus *}
	{if $conf.disqus}
		<script src="//{$conf.disqus}.disqus.com/embed.js" async></script>
	{/if}

</head>
<body data-spy="scroll" data-target="#nav-content" data-offset="76">
<div class="app">
	<!--[if lt IE 10]>
	<div class="page-message" role="alert">You are using an <strong>outdated</strong> browser. Please <a class="alert-link" href="http://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</div>
	<![endif]-->

