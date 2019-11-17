<div id="modal-move" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Move page</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<div style="margin-bottom: 8px;" class="clearfix">
					<a href="/page/move/{$page.id}/0" class="btn btn-primary" style="float: right;">Move</a>
					<div style="padding-top: 0.5rem; margin-right: 100px;">
						<i class="fas fa-home"></i> Homepage
					</div>
				</div>
				{$parentSubLevelId=0}
				{include file="page/getSubLevels.tpl"}
			</div>
		</div>
	</div>
</div>
