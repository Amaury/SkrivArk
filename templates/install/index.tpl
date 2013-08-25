{include file="inc.header.tpl"}

<div class="container">
	<h2>Ark <small>Installation</small></h2>
	<p>
		Thank you for choosing <em>Ark</em>. As you'll see, it's a very simple yet powerful wiki engine.
	</p>
	<p>
		Please consider these preriquisites:
	</p>
	<ul>
		<li>PHP &gt;= 5.3</li>
		<li>A MySQL (or MariaDB) database server</li>
		<li>A Memcache server</li>
	</ul>

	<h2 style="margin-top: 40px;">Steps <small>of installation</small></h2>
	{if !$editableConfig}
		<p>
			Add writing rights to the <tt class="label">etc/temma.json</tt> file.
		</p>
		<a href="/install" class="btn btn-primary">Refresh this page</a>
	{else}
		<p>
			The installation process has 4 simple steps:
		</p>
		<ol>
			<li>Database configuration</li>
			<li>Memcache configuration</li>
			<li>Site parameters</li>
			<li>Creation of a default aministrator user</li>
		</ol>
		<a href="/install/step1" class="btn btn-primary">Proceed to step 1</a>
	{/if}
</div>

{include file="inc.footer.tpl"}
