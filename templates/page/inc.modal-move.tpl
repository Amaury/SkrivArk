<div id="modal-move" class="modal hide fade" data-keyboard="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3>Move page</h3>
	</div>
	<div class="modal-body">
		<div style="margin-bottom: 8px;">
			<i class="icon-home"></i> Homepage
			<button class="btn" onclick="ark.movePage({$page.id}, 0)">Move</button>
		</div>
		{assign var="parentSubLevelId" value=0}
		{include file="page/getSubLevels.tpl"}
	</div>
	<div class="modal-footer">
		<button class="btn" onclick="$('#modal-move').modal('hide')">Cancel</a>
	</div>
</div>
