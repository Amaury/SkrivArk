<aside class="app-aside app-aside-expand-md app-aside-light">
	<div class="aside-content">
		<div class="aside-menu p-3" style="padding-top: 0;">
			<nav id="stacked-menu" class="stacked-menu" style="padding-top: 0;">
				<ul class="menu">
					{if $ACTION == "versions" && $versions}
						{* ****************** VERSIONS OF A PAGE ********************** *}
						<li class="menu-header" style="margin-top: 0;"><a href="/page/show/{$page.id}" class="btn btn-success"><i class="fas fa-reply"></i> Back to the page</a></li>
						<script>{literal}
							function loadPage(versionId, url) {
								$("#link-version-" + versionId).attr("href", url);
								document.location.href = url;
							}
						{/literal}</script>
						{*<ul class="nav nav-tabs nav-stacked" style="background-color: #f8f8f8;">*}
							{foreach name="versions" from=$versions item=version}
								<li style="margin: 0; padding: 0; border-top: 1px solid #ccc; background-color: {if $version.id == $versionFrom}#ddffdd{elseif $version.id == $versionTo}#fdd{else}{cycle values="#ddd,#eee"}{/if}; {if $version.id == $versionFrom || $version.id == $versionTo}font-weight: bold;{/if}"
								 title="{if $version.id == $versionFrom}This is the version of reference{elseif $version.id == $versionTo}This is the compared version{else}Define the version to compare{/if}">
									<a id="link-version-{$version.id}"
									 {if $version.id == $versionFrom || $version.id == $versionTo}href="#" onclick="return false"
									 {else}href="/page/versions/{$page.id}/{$versionFrom}/{$version.id}"{/if}>
										{if $smarty.foreach.versions.index}
											<i class="fas fa-pencil-alt" onclick="loadPage({$version.id}, '/page/edit/{$page.id}/{$version.id}')"
											 title="Edit the page from this version" style="float: right; margin-left: 4px; cursor: pointer; margin-top: 4px;"></i>
										{else}
											<span class="float-right" style="width: 18px;">&nbsp;</span>
										{/if}
										{if $version.id == $versionFrom}
											<i class="fas fa-plus"></i>
											<span style="width: 18px;">&nbsp;</span>
										{elseif $version.id == $versionTo}
											<i class="fas fa-minus"></i>
											<span style="width: 18px;">&nbsp;</span>
										{else}
											<span class="float-left" style="width: 18px; height: 36px;">&nbsp;</span>
											<i class="fas fa-map-marker float-right" title="Define the version of reference" style="margin-top: 4px;"
											 onclick="loadPage({$version.id}, '/page/versions/{$page.id}/{$version.id}/{$versionTo}')"></i>
										{/if}
										{$version.creationDate|date_format:"%Y-%m-%d %H:%M"}<br />{$version.name|escape}
									</a>
								</li>
							{/foreach}
						{*</ul>*}
						<div style="margin-left: 5em; font-size: 0.7em; color: #888;">
							<i class="fas fa-plus"></i> Version of reference<br />
							<i class="fas fa-minus"></i> Compared version<br />
							{if $versions|@count > 2}
								<i class="fas fa-map-marker"></i> Use this version as comparison reference<br />
								<i class="fas fa-hand-up"></i> Click on a version to use it for comparison<br />
							{/if}
							<i class="fas fa-pencil-alt"></i> Edit the page from this version
						</div>
					{elseif !$page && $user}
						<li class="menu-header" style="margin-top: 0;"><a href="/page/create/0" class="btn btn-success" title="Add a sub-page"><i class="fas fa-plus"></i></a></li>
					{elseif $page}
						{if $ACTION == "version" || $user}
							<li class="menu-header" style="margin-top: 0;">
								{if $ACTION == "version"}
									<a href="/page/show/{$page.id}" class="btn btn-info" title="See the last version"><i class="fas fa-eye"></i></a>
								{elseif $user}
									<a href="/page/create/{$page.id}" class="btn btn-success" title="Add a sub-page"><i class="fas fa-plus"></i></a>
									<a href="/page/edit/{$page.id}" class="btn btn-warning" title="Edit this page"><i class="fas fa-pencil-alt"></i></a>
									<button class="btn btn-dark" data-toggle="modal" data-target="#modal-move" title="Move this page"><i class="fas fa-arrows-alt"></i></button>
									{if $page.nbrVersions > 1}
										<a href="/page/versions/{$page.id}" class="btn btn-primary" title="View the {$page.nbrVersions} versions"><i class="fas fa-tasks"></i></a>
									{/if}
									{if !$subPages}
										<a href="/page/remove/{$page.id}" onclick="return confirm('Delete this page?')" class="btn btn-danger" title="Delete this page"><i class="fas fa-trash"></i></a>
									{/if}
								{/if}
							</li>
						{/if}
						{* breadcrumbs *}
						<li class="menu-header"><a href="/"><i class="fas fa-home"></i> Home</a></li>
						{foreach $breadcrumb as $crumb}
							<li class="menu-header" style="margin-top: 0; padding-top: 0;"><a href="/page/show/{$crumb.id}"><i class="fas {*fa-caret-right*} fa-reply fa-rotate-180"></i> {$crumb.title|escape}</a></li>
						{/foreach}
						{if !$showAsSubPage}
							<li class="menu-header" style="text-transform: none; padding-top: 0; margin-top: 0;"><a href="$URL"><i class="fas {*fa-chevron-right*} fa-reply-all fa-rotate-180"></i> {$page.title|escape}</a></li>
						{/if}
					{/if}
				</ul>
				{* list of subpages *}
				{if $ACTION != 'version' && $subPages}
					<ul class="menu _pages-sortable">
						{foreach name=subPages from=$subPages item=subPage}
							<li id="subpage-{$subPage.id}" title="{$subPage.intro|escape}"
							 class="menu-item {if $showAsSubPage && $subPage.id == $page.id}has-active{/if}">
								<a href="/page/show/{$subPage.id}/{$subPage.titledUrl}" class="menu-link" style="padding-right: 0;"><span class="menu-text">
									{if $subPage.nbrChildren}
										<i class="fas fa-caret-right" style="float: right; opacity: 0.5;"></i>
									{/if}
									{$subPage.title|escape}
								</span></a>
							</li>
						{/foreach}
					</ul>
				{/if}
			</nav>
		</div>
		{if $user && $subPages|@count > 1}
			<footer class="aside-footer p-3">
				<span style="font-size: 0.8rem; color: gray;">You can order subpages using drag n'drop.</span>
			</footer>
		{/if}
	</div>
</aside>

