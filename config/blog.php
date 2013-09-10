<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Blog Variables
| -------------------------------------------------------------------------
|
| Configure extended blog functionality
|
| Full details of configurable options are available at
| http://docs.nailsapp.co.uk/modules/blog/config
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
		$config['blog_post_associations'][0]->widget_title	= 'The title of the widget in the front end ';
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
	- The name of the lable column, i.e what will be shown in the list. specify an array of column
	  and they'll be merged into one joined with a space.

		$config['blog_post_associations'][0]->source			= new stdClass();
		$config['blog_post_associations'][0]->source->table		= 'hills';
		$config['blog_post_associations'][0]->source->id		= 'id';
		$config['blog_post_associations'][0]->source->label		= 'name';

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

	You can get the associations by calling blog_post_associations(), which accepts two required parameters:

	 - $post_id
	 - $association_index (the index of this config array which you're interested in)

	You can optionally define (in this array) the model and method which the helper should use when returning data
	(if none are specified or the method is not callable then the raw IDs will be returned):

		$config['blog_post_associations'][0]->model		= 'hill_model';
		$config['blog_post_associations'][0]->method	= 'get_by_ids';

	This method will be passed an array of IDs and the output of this method will be returned by the helper.

	A third boolean parameter can also be defined:

	 - $return_html

	If you set this to TRUE then an additional config item must be set, the view to load:

		$config['blog_post_associations'][0]->view	= 'blog/associations/hill';

	This view will be passed the data set (either an array of IDs or the result of the model/method specified earlier)
	as a parameter called `$data`. If no view is set or the view is invalid an empty string will be returned.


*/