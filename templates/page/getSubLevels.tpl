{if $subLevelPages}
	<div id="move-list-{$parentSubLevelId}">
		<form class="form-inline" onsubmit="return false" style="margin-bottom: 8px;">
			<select id="sel-move-{$parentSubLevelId}" onchange="ark.selectMove({$page.id}, {$parentSubLevelId}, this.value)" style="width: 400px;">
				<option value="0">Choose a page...</option>
				{foreach from=$subLevelPages item=subLevel}
					{if $subLevel.id != $page.id}
						<option value="{$subLevel.id}">{$subLevel.title|escape}</option>
					{/if}
				{/foreach}
			</select>
			<button id="btn-move-{$parentSubLevelId}" onclick="ark.movePage({$page.id}, {$parentSubLevelId})" class="btn hide">Move</button>
		</form>
	</div>
	<div id="move-sub-{$parentSubLevelId}"></div>
{/if}
