$(document).ready(function()
{
		// Handler for .ready() called.
		$.getJSON('/json/getFullGameboard', function(data)
		{
				var allowed_states = [1, 2, 3, 4, 5];

				for (var x in data.board)
				{
						var tile = data.board[x];
						for (var c in allowed_states)
						{
								var current_state = allowed_states[c];
								if (tile.state == current_state)
								{
										$("#tile_"+tile.offset+"_"+current_state).show();
								}
								else
								{
										$("#tile_"+tile.offset+"_"+current_state).hide();
								}
						}
				}
				$("#loading").hide();
		})
});
