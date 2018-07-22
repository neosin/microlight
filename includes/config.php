<?php

if (!defined('MICROLIGHT_INIT')) die();

class Config {
	/*
	 * Modify these variables before installing on your website:
	 */

	// DB File (string):
	// The location of the SQLite database file, relative to root.
	// default: 'microlight.db'
	const DB_FILE = 'microlight.db';

	// Posts Per Page (integer):
	// How many posts should be shown on the homepage or while searching
	// default: 20
	const POSTS_PER_PAGE = 20;

	// Theme (string):
	// The folder name of the theme you would like to use for this blog
	// default: 'uberlight'
	const THEME = 'uberlight';

	// Title Separator (string):
	// What splits up your name and the post name in the title bar
	// eg. "Your Name | Post Title"
	// default: ' | '
	const TITLE_SEPARATOR = ' | ';
}

/**********************************
 *  DO NOT EDIT BELOW THIS POINT  *
 **********************************/

require_once('lib/enum.php');
require_once('db.include.php');
require_once('functions.include.php');

abstract class Show extends BasicEnum {
	const ARCHIVE = 'ARCHIVE';
	const POST = 'POST';
	const PAGE = 'PAGE';
	const ERROR404 = 'ERROR404';
}

$post_slug = '';
$post_tag = '';
$post_type = '';
$search_query = '';
$pagination = null;
$showing = Show::ARCHIVE;
$db = null;
$Me = null;
$Posts = null;
