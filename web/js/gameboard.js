function setState(id, newState)
{
	/*
	const PUB_UP = 1;
	const PUB_FLAG = 2;
	const PUB_QUESTION = 3;
	const PUB_BOMB = 4;
	const PUB_EMPTY = 10;
	const PUB_1 = 11;
	const PUB_2 = 12;
	const PUB_3 = 13;
	const PUB_4 = 14;
	const PUB_5 = 15;
	const PUB_6 = 16;
	const PUB_7 = 17;
	const PUB_8 = 18;
	*/
	
	var icon;
	
	switch (newState)
	{
		case 1: icon = 'up'; break;
		case 2: icon = 'up-flag'; break;
		case 3: icon = 'up-question'; break;
		case 4: icon = 'bomb'; break;
		case 10: icon = 'empty'; break;
		case 11: icon = '1'; break;
		case 12: icon = '2'; break;
		case 13: icon = '3'; break;
		case 14: icon = '4'; break;
		case 15: icon = '5'; break;
		case 16: icon = '6'; break;
		case 17: icon = '7'; break;
		case 18: icon = '8'; break;
	}
	
	$(id+' img').attr('src', '/images/'+icon+'.png');
}

$(document).ready(function()
{
	// Handler for .ready() called.
	$.getJSON('/json/getFullGameboard', function(data)
	{
		for (var x in data.board)
		{
			var tile = data.board[x];
			setState('#tile_'+tile.offset, tile.state);
		}
		$('#loading').hide();
	})
});
