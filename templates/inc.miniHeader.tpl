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
	{*<link rel="icon" type="image/png" href="/favicon.png" />*}
	<link rel="shortcut icon" href="/favicon.ico">
	<meta name="theme-color" content="#070B0F">
	{* BEGIN PLUGINS STYLES *}
	<link rel="stylesheet" href="/css/open-iconic-bootstrap.min.css">
	<link rel="stylesheet" href="/vendors/fontawesome/css/all.css">
	{* BEGIN THEME STYLES *}
	{if $conf.darkTheme}
		<link rel="stylesheet" href="/css/theme-dark.min.css" data-skin="default">
		<link href="/css/style-dark.css" rel="stylesheet" media="screen" />
	{else}
		<link rel="stylesheet" href="/css/theme.min.css" data-skin="default">
		<link href="/css/style.css" rel="stylesheet" media="screen" />
	{/if}
	{* main *}
	<script src="/js/ark.js"></script>
	{* Disqus *}
	{if $conf.disqus}
		<script src="//{$conf.disqus}.disqus.com/embed.js" async></script>
	{/if}
	{* WYSIWYG editor *}
	{if $CONTROLLER == 'page' && ($ACTION == 'edit' || $ACTION == 'create')}
		<link href="/vendors/summernote/summernote-bs4.css" rel="stylesheet">
	{/if}
	{* code prettifier *}
	{if $URL == '/' || ($CONTROLLER == 'page' && $ACTION == 'show')}
		<link rel="stylesheet" type="text/css" href="/vendors/google-code-prettify/prettify.css">
	{/if}
	{* font management *}
	{if $conf.fontname || $conf.titlesfontname}
		<link href="https://fonts.googleapis.com/css?family={$conf.fontname|escape:'url'}{if $conf.fontname && $conf.titlesfontname}|{/if}{$conf.titlesfontname|escape:'url'}&display=swap"
		 rel="stylesheet">
	{/if}
	{if $conf.fontsize || $conf.fontname || $conf.textsize || $conf.titlesfontname || $conf.titlessize}
		<style type="text/css">
			{literal}html {{/literal}
				{if $conf.fontsize}
					font-size: {$conf.fontsize}px;
				{/if}
			{literal}}{/literal}
			{*literal}#content p, #content li, #content th, #content td {{/literal*}
			{literal}#content {{/literal}
				{if $conf.fontname}
					font-family: '{$conf.fontname}';
				{/if}
				{if $conf.textsize}
					font-size: {$conf.textsize}rem !important;
				{/if}
			{literal}}{/literal}
			{if $conf.titlesfontname}
				{literal}.page-inner h1, .page-inner h2, .page-inner h3, .page-inner h4 {{/literal}
					font-family: '{$conf.titlesfontname}' !important;
				{literal}}{/literal}
			{/if}
			{if $conf.titlessize}
				{if $conf.titlessize == 1.25}
					{$h2size = 0.8}
					{$h3size = 0.65}
					{$h4size = 0.45}
				{elseif $conf.titlessize == 1.5}
					{$h2size = 1}
					{$h3size = 0.75}
					{$h4size = 0.5}
				{elseif $conf.titlessize == 1.75}
					{$h2size = 1.25}
					{$h3size = 1}
					{$h4size = 0.75}
				{elseif $conf.titlessize == 2}
					{$h2size = 1.5}
					{$h3size = 1.25}
					{$h4size = 1}
				{elseif $conf.titlessize == 2.25}
					{$h2size = 1.75}
					{$h3size = 1.5}
					{$h4size = 1.25}
				{elseif $conf.titlessize == 2.5}
					{$h2size = 2}
					{$h3size = 1.75}
					{$h4size = 1.5}
				{elseif $conf.titlessize == 2.75}
					{$h2size = 2.25}
					{$h3size = 2}
					{$h4size = 1.75}
				{elseif $conf.titlessize == 3}
					{$h2size = 2.5}
					{$h3size = 2.25}
					{$h4size = 2}
				{elseif $conf.titlessize == 3.5}
					{$h2size = 3}
					{$h3size = 2.75}
					{$h4size = 2.25}
				{elseif $conf.titlessize == 4}
					{$h2size = 3.5}
					{$h3size = 3}
					{$h4size = 2.5}
				{elseif $conf.titlessize == 5}
					{$h2size = 4}
					{$h3size = 3.5}
					{$h4size = 3}
				{elseif $conf.titlessize == 6}
					{$h2size = 5}
					{$h3size = 4}
					{$h4size = 3.5}
				{elseif $conf.titlessize == 8}
					{$h2size = 6.5}
					{$h3size = 5}
					{$h4size = 4}
				{elseif $conf.titlessize == 10}
					{$h2size = 8}
					{$h3size = 6.5}
					{$h4size = 5}
				{/if}
				{literal}.page-inner h1 {{/literal}
					font-size: {$conf.titlessize}rem !important;
				{literal}}{/literal}
				{literal}.page-inner h2 {{/literal}
					font-size: {$h2size}rem !important;
				{literal}}{/literal}
				{literal}.page-inner h3{{/literal}
					font-size: {$h3size}rem !important;
				{literal}}{/literal}
				{literal}.page-inner h4{{/literal}
					font-size: {$h4size}rem !important;
				{literal}}{/literal}
			{/if}
		</style>
	{/if}
</head>
<body data-spy="scroll" data-target="#nav-content" data-offset="76">
<div class="app">
	<!--[if lt IE 10]>
	<div class="page-message" role="alert">You are using an <strong>outdated</strong> browser. Please <a class="alert-link" href="http://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</div>
	<![endif]-->

