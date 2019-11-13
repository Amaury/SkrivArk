{include file="inc.header.tpl"}

<main class="app-main" style="font-size: 1rem;">
	<div class="container" style="padding-top: 2.4rem;">
		<h2>Ark <small>Installation</small></h2>
		<p>
			Thank you for choosing <em>Ark</em>. As you'll see, it's a very simple yet powerful wiki engine.
		</p>
		<p>
			Please consider these prerequisites:
		</p>
		<ul>
			<li><a href="https://www.php.net/" target="_blank">PHP</a> &gt;= 7.3</li>
			<li>A <a href="https://www.mysql.com/" target="_blank">MySQL</a> (or <a href="https://mariadb.com/" target="_blank">MariaDB</a>) database server</li>
			<li>Optional: A <a href="https://memcached.org/" target="_blank">Memcache</a> or <a href="https://redis.io/" target="_blank">Redis</a> server (for session storage)</li>
		</ul>

		<h2 style="margin-top: 40px;">Steps <small>of installation</small></h2>
		{if !$writableTemma || !$writableLog || !$writableTmp || !$writableSplash}
			<p>
				Before going further, <em>Ark</em> needs full <strong>write access</strong> to the following file(s) and directorie(s):
			</p>
			<ul>
				{if !$writableTemma}
					<li><tt>etc/temma.json</tt></li>
				{/if}
				{if !$writableLog}
					<li><tt>log/</tt></li>
				{/if}
				{if !$writableTmp}
					<li><tt>tmp/</tt></li>
				{/if}
				{if !$writableSplash}
					<li><tt>var/splashscreen.html</tt></li>
				{/if}
			</ul>
			<p class="mb-0">
				It could be done through an administration panel, or using this command line:
			</p>
			<div style="padding: 0.4rem; background-color: rgba(255, 255, 255, 0.6); color: #444;"><tt>
				chmod 777
				{if !$writableTemma}etc/temma.json{/if}
				{if !$writableLog}log{/if}
				{if !$writableTmp}tmp{/if}
				{if !$writableSplash}var/splashscreen.html{/if}
			</tt></div>
			<p class="pt-3">
				<a href="/install" class="btn btn-primary">Refresh this page</a>
			</p>
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
</main>

{include file="inc.footer.tpl"}
