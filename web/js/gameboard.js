// Changes a tile
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
	
	$('img#tile_'+id).attr('src', '/images/'+icon+'.png');

	switch (newState)
	{
		case 1:
		case 2:
		case 3:
			$('img#tile_'+id).toggleClass('clickable', true);
			break;
		case 4:
		case 10:
		case 11:
		case 12:
		case 13:
		case 14:
		case 15:
		case 16:
		case 17:
		case 18:
			$('img#tile_'+id).toggleClass('clickable', false);
			break;
	}
}

function changeStatus(color, message)
{
		$('#status > span').removeClass();
		$('#status > span').text(message);
		$('#status > span').addClass(color);
}

function clickTile(id)
{
	var offset = id.substring('tile_'.length, id.length);
	if ($('#'+id).hasClass('clickable'))
	{
		// Flag a tile
		if ($('#flag_on').is(':visible'))
		{
				changeStatus('grey', 'Loading...');
				$.getJSON('/json/flagTile?offset='+offset, function(data)
				{
						for (var x in data.board) {
								var tile = data.board[x];
								setState(tile.offset, tile.state);
						}
						switch (data.result) {
						case 3:
								changeStatus('grey', 'Tile flagged');
								break;
						case 4:
								changeStatus('grey', 'Tile unflagged');
								break;
						default:
								changeStatus('red', 'Unknown error');
						}
				})
		}
		// Question a tile
		else if ($('#question_on').is(':visible'))
		{
				changeStatus('grey', 'Loading...');
				$.getJSON('/json/questionTile?offset='+offset, function(data)
				{
						for (var x in data.board) {
								var tile = data.board[x];
								setState(tile.offset, tile.state);
						}
						switch (data.result) {
						case 5:
								changeStatus('grey', 'Tile questioned');
								break;
						case 6:
								changeStatus('grey', 'Tile unquestioned');
								break;
						default:
								changeStatus('red', 'Unknown error');
						}
				})
		}
		// Click a tile
		else
		{
				changeStatus('grey', 'Loading...');
				$.getJSON('/json/clickTile?offset='+offset, function(data)
				{
						for (var x in data.board) {
								var tile = data.board[x];
								setState(tile.offset, tile.state);
						}
						switch (data.result) {
						case 2:
								changeStatus('grey', '--');
								break;
						case 7:
								changeStatus('grey', 'Tile revealed');
								break;
						case 8:
								changeStatus('green', 'Game won!');
								break;
						case 9:
								changeStatus('red', 'Game lost!');
								break;
						default:
								changeStatus('red', 'Unknown error');
						}
				})
		}
	}
}

function switchFlag()
{
	if ($('#flag_on').is(':visible'))
  {
		$('#flag_on').hide();
		$('#flag_off').show();
	}
  else
	{
		$('#flag_off').hide();
		$('#flag_on').show();
		$('#question_on').hide();
		$('#question_off').show();
	}
}

function switchQuestion()
{
	if ($('#question_on').is(':visible'))
  {
		$('#question_on').hide();
		$('#question_off').show();
	}
  else
	{
		$('#question_off').hide();
		$('#question_on').show();
		$('#flag_on').hide();
		$('#flag_off').show();
	}
}

// On page load
$(document).ready(function()
{
	// Handler for .ready() called.
	$.getJSON('/json/getFullGameboard', function(data)
	{
		for (var x in data.board)
		{
			var tile = data.board[x];
			setState(tile.offset, tile.state);
		}
		if (data.result == 9)
		{
			changeStatus('red', 'Game lost!');
		}
		else
		{
			changeStatus('grey', '--');
		}
	})

	// When the user click on a tile
	$('table.board img').click(function()
	{
		clickTile(this.id);
	});
});

// Handler for keys pressed
$(document).keypress(function(event)
{
		switch (event.which)
		{
		case 102:
				switchFlag();
				break;
		case 113:
				switchQuestion();
				break;
		}
});
