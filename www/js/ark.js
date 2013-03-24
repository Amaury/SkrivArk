var ark = new function() {
	/**
	 * Handles page moving.
	 * @param	int	pageId	Current page identifier.
	 * @param	int	id	Parent identifier.
	 * @param	int	subId	Selected identifier.
	 */
	this.selectMove = function(pageId, id, subId) {
		if (subId == 0) {
			$("#move-sub-" + id).hide();
			$("#btn-move-" + id).hide();
			return;
		}
		$("#btn-move-" + id).show();
		$("#move-sub-" + id).load("/page/getSubLevels/" + pageId + "/" + subId, function() {
			$("#move-sub-" + id).show();
		});
	};
	/**
	 * Ask for page move.
	 * @param	int	pageId	Current page identifier.
	 * @param	int	levelId	Identifier of the parent chosen level.
	 */
	this.movePage = function(pageId, levelId) {
		var destinationId = 0;
		if (levelId)
			destinationId = $("#sel-move-" + levelId).val();
		document.location.href = "/page/move/" + pageId + "/" + destinationId;
	};
};
