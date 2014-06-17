<style type="text/css">

	hr
	{
		border-top:1px solid #DDD;
	}
	h3
	{
		margin-top:0;
	}
	h4
	{
	}
	h5
	{
		margin-top:2.5em;
		font-size:1.3em;
	}
	.jumbotron p
	{
		font-size:1.2em;
	}
	ul.list-group li
	{
		cursor:pointer;
	}

	ul.list-group p
	{
		font-size:1em;
		margin-bottom:0;
	}

	ul.list-group pre
	{
		margin-top:1em;
		max-height:250px;
		overflow:auto;
	}

</style>
<script type="text/javascript">

	function addEventHandler(elem,eventType,handler)
	{
		if (elem.addEventListener)
		{
			elem.addEventListener (eventType,handler,false);
		}
		else if (elem.attachEvent)
		{
			elem.attachEvent ('on'+eventType,handler);
		}
	}

	// --------------------------------------------------------------------------

	if ( typeof( document.getElementsByClassName ) === 'function' )
	{
		var _pre,_switch,_id,_data;

		//	Hide <pre>'s
		_pre = document.getElementsByClassName( 'data-render' );

		for ( var i = 0; i < _pre.length; i++ )
		{
			_pre[i].style.display = 'none';
		}

		//	Bind to switches
		_switch = document.getElementsByClassName( 'data-switch' );

		for ( var i = 0; i < _switch.length; i++ )
		{
			addEventHandler( _switch[i], 'click', function()
			{
				_id		= this.id;
				_data	= document.getElementById( _id + '-pre' );

				if ( _data.style.display === 'none' )
				{
					_data.style.display = 'block';
				}
				else
				{
					_data.style.display = 'none';
				}
			});
		}
	}

</script>