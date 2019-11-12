{if $subLevelPages}
	<div id="move-list-{$parentSubLevelId}" class="clearfix">
		<form class="form-inline" onsubmit="return false" style="display: block; margin-bottom: 8px;">
			<button id="btn-move-{$parentSubLevelId}" onclick="ark.movePage({$page.id}, {$parentSubLevelId})" class="btn btn-primary" style="display: none; float: right;">Move</button>
			<div style="margin-right: 100px;">
				<select id="sel-move-{$parentSubLevelId}" onchange="ark.selectMove({$page.id}, {$parentSubLevelId}, this.value)" class="form-control" style="width: 400px;">
					<option value="0">Choose a page...</option>
					{foreach from=$subLevelPages item=subLevel}
						{if $subLevel.id != $page.id}
							<option value="{$subLevel.id}">{$subLevel.title|escape}</option>
						{/if}
					{/foreach}
				</select>
			</div>
		</form>
	</div>
	<div id="move-sub-{$parentSubLevelId}"></div>
{/if}
