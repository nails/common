<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Blog Variables
| -------------------------------------------------------------------------
|
| Configure extended blog functionality
|
| Full details of configurable options are available at
| TODO: link to docs
|
*/

	$config = array();

/*

	Blog Post Associations
	======================

	Configure the blog module to create associations between posts

	Define the Fieldset Strings
	---------------------------

	Each association will have it's own `<fieldset>`, set some descriptive text for the user.
	These fields can be `lang()` identifiers, if it returns an empty lang (set in blog_lang.php)
	then it'll default to just rendering the string. Comprende?

		$config['blog_post_associations']					= array();
		$config['blog_post_associations'][0]				= new stdClass();
		$config['blog_post_associations'][0]->slug			= 'a-unique-slug';
		$config['blog_post_associations'][0]->sidebar_title	= 'The title of the widget in the front end sidebar';
		$config['blog_post_associations'][0]->legend		= 'The <fieldset>\'s legend';
		$config['blog_post_associations'][0]->description	= 'The <fieldset>\'s description';
		$config['blog_post_associations'][0]->multiple		= TRUE;

	Note: the following examples use the notion that there is a table with a list of hills and that the blog
	writer might want to associate blog posts to hills (and vice versa).



	Define the Data Source
	----------------------

	We'll need to define the source. For single table associations this is straight-forward, specify:

	- The name of the table
	- The name of the column which is the ID (usually ID if you're following any kind of sensible norm)
	- The name of the label column, i.e what will be shown in the list. specify an array of column
	  and they'll be merged into one joined with a space.
	- The `WHERE` field, if any.

		$config['blog_post_associations'][0]->source			= new stdClass();
		$config['blog_post_associations'][0]->source->table		= 'hills';
		$config['blog_post_associations'][0]->source->id		= 'id';
		$config['blog_post_associations'][0]->source->label		= 'name';
		$config['blog_post_associations'][0]->source->where		= '';

	If you need to run a complex query to get this information you can specify a `sql` property
	so long as it returns a field called `id` and another called `label`. If you use this approach
	you will also need to provide a property called `sql_where` which is the `WHERE` part of the query
	(used when fetching the associated content) and will be appended to the `sql` query (no need to specify
	the `WHERE` keyword).

	Example: A list of hills with their location in backets. The `hill` table refers to the `location`
	table via a field called `location_id`.

		$config['blog_post_associations'][0]->source->sql		= 'SELECT h.id id, CONCAT( h.name, ' (', l.label, ')') label FROM `hill` h JOIN `location` l ON l.id = h.location_id';
		$config['blog_post_associations'][0]->source->sql_where	= 'h.id = %s';



	Define the Target Table (where the association is saved)
	--------------------------------------------------------

	The `target` object tells the blog model where to save this assication. It expects the table
	to be in the format:

		id | post_id | associated_id

	Note: make sure appropriate foreign keys are defined as appropriate

		$config['blog_post_associations'][0]->target		= 'blog_post_hill'



	Helpers / Rendering
	-----------------------

	The blog sidebar will automatically render a list of the associations, by default simply as a text list.
	If you want or need to change this behaviour you must specify a callback in this config. This callback
	can either be called per item or it can be called once and passed an array of IDs.

	To call it once per ID define the following:

		$config['blog_post_associations'][0]->widget->callback = function( $id, $label, $index ) { return 'formatted string' };

	Note that this function must return the HTML which'll be placed in a <li></li>.

	Alternatively, you can define `callback_batch`, like so:

		$config['blog_post_associations'][0]->widget->callback_batch = function( $ids ) { return 'formatted string' };

	This will be passed an array of objects, each with two properties: `id` and `label`; you are responsible for rendering *all* the HTML.

*/