<?php

if (!defined('MICROLIGHT_INIT')) die();

require_once('sql.include.php');

class DBError extends Exception {}

class DB {
	public $db;
	public $sql;

	function __construct () {
		$this->db = new PDO('sqlite:' . Config::DB_FILE);
		$this->sql = new SQL($this->db);
	}

	public function close() {
		$this->db = null;
	}
}

class Model {
	private $table_name = ''; // Inherited classes must set this.
	private $db;
	private $sql;

	function __construct(&$db, $table_name) {
		$this->db = $db->db;
		$this->sql = $db->sql;
		$this->table_name = $table_name;
	}

	// Main "SELECT" function, to fetch data from the DB
	function find ($where = [], $limit = -1, $offset = 0) {
		$sql = "SELECT * FROM $this->table_name";
		$sql .= $this->sql->where($where);
		$sql .= " LIMIT $limit OFFSET $offset";
		$stmt = $this->db->query($sql, PDO::FETCH_OBJ);
		return $stmt->fetchAll();
	}

	// Essentially the same as the "find" function but will return a single
	// object, instead of an array.
	function findOne ($where = [], $offset = 0) {
		$results = $this->find($where, 1, $offset);
		if (count($results) > 0) return $results[0];
		return NULL;
	}
}

class Identity extends Model {
	public $table_name = 'identity';

	function __construct (&$db) {
		parent::__construct($db, $this->table_name);

		// Create the table if it does not already exist

		// We should assume that this table already exists, and should
		// therefore not be run every time the blog is loaded.
		/*
		$db->db->exec($db->sql->create($this->table_name, [
			[
				'column' => 'id',
				'type' => SQL::PRIMARY_KEY_TYPE
			],
			[
				'column' => 'name',
				'type' => SQL::TEXT_TYPE . SQL::NOT_NULL
			],
			[
				'column' => 'email',
				'type' => SQL::TEXT_TYPE
			],
			[
				'column' => 'note',
				'type' => SQL::TEXT_TYPE
			]
		]));
		*/
	}
}

class RelMe extends Model {
	public $table_name = 'relme';

	function __construct (&$db) {
		parent::__construct($db, $this->table_name);

		// We should assume that this table already exists, and should
		// therefore not be run every time the blog is loaded.
		/*
		$db->db->exec($db->sql->create($this->table_name, [
			[
				'column' => 'id',
				'type' => SQL::PRIMARY_KEY_TYPE
			],
			[
				'column' => 'name',
				'type' => SQL::TEXT_TYPE
			],
			[
				'column' => 'url',
				'type' => SQL::TEXT_TYPE . SQL::NOT_NULL
			]
		], [
			[
				'table' => 'identity',
				'reference' => 'id'
			]
		]));
		*/
	}
}

class Post extends Model {
	public $table_name = 'post';

	function __construct (&$db) {
		parent::__construct($db, $this->table_name);

		/*
		$db->db->exec($db->sql->create($this->table_name, [
			[
				'column' => 'id',
				'type' => SQL::PRIMARY_KEY_TYPE
			],
			[
				// Post Title
				'column' => 'name',
				'type' => SQL::TEXT_TYPE
			],
			[
				// Markdown post contents
				'column' => 'content',
				'type' => SQL::TEXT_TYPE . SQL::NOT_NULL
			],
			[
				// Post Type
				'column' => 'type',
				'type' => SQL::TEXT_TYPE . SQL::NOT_NULL
			],
			[
				// URL friendly copy of the title
				'column' => 'slug',
				'type' => SQL::TEXT_TYPE . SQL::NOT_NULL
			],
			[
				// Date/Time ISO8601
				'column' => 'published',
				'type' => SQL::TEXT_TYPE . SQL::NOT_NULL
			],
			[
				// Comma separated tags
				'column' => 'tags',
				'type' => SQL::TEXT_TYPE
			],
			[
				// "lat,long", otherwise "Address"
				'column' => 'location',
				'type' => SQL::TEXT_TYPE
			],
			[
				// If the post directly refers to a specific
				// location on the internet, here is where to
				// put it.
				'column' => 'url',
				'type' => SQL::TEXT_TYPE
			]
		], [
			// A post must be made by an identity, although there
			// should only ever be one identity.
			[
				'table' => 'identity',
				'reference' => 'id'
			]
		]));
		*/
	}

	function find ($where = [], $limit = -1, $offset = 0) {
		$results = parent::find($where, $limit, $offset);
		// Process each result
		foreach ($results as $key => $value) {
			// Split the commas in the tags into an array
			$results[$key]->tags = explode(',', $value->tags);
		}
		return $results;
	}
}
