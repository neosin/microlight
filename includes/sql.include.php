<?php

// SQL Helper Functions
// ----
// These functions will simply help create some SQL queries, like creating
// tables (eg. `CREATE TABLE IF NOT EXISTS ...`)
//
// **These functions should only be used internally and not rely on user input.**

if (!defined('MICROLIGHT_INIT')) die();

class SQL {
	private $db;

	function __construct(&$db) {
		$this->db = $db;
	}

	private function regex_test($regex, $test) {
		// If the regex doesn't match just throw an exception
		if (!preg_match($regex, $test)) throw new Exception('Value "' . $test . '" invalid', 1);
	}

	private function propsToString ($properties) {
		array_walk($properties, function ($property, $key) use (&$acc) {
			// Make sure "type" only contains alpha chars or a space
			$this->regex_test('/^[a-zA-Z ]+$/', $property['type']);

			// Same again but the column name may have an underscore instead
			$this->regex_test('/^[a-zA-Z_]+$/', $property['column']);

			// Don't put a comma on the first element
			if ($key !== 0) $acc .= ', ';

			// Append the column name and its type
			$acc .= '`' . $property['column'] . '` ' . strtoupper($property['type']);
		});

		return $acc;
	}

	private function foreignKeyToString ($foreign_keys) {
		array_walk($foreign_keys, function ($foreign_key_properties) use (&$acc) {
			// The table to refer to
			$table = $foreign_key_properties['table'];

			// The column name in the current table
			$column = $foreign_key_properties['column'];

			// The column name from the foreign table
			$reference = $foreign_key_properties['reference'];

			// Check all three props
			$this->regex_test('/^[a-zA-Z_]+$/', $table);
			$this->regex_test('/^[a-zA-Z_]+$/', $column);
			$this->regex_test('/^[a-zA-Z_]+$/', $reference);

			$acc .= ', FOREIGN KEY(`' . $column . '`) REFERENCES `' . $table
				. '`(`' . $reference . '`)';
		});

		return $acc;
	}

	public function create ($table_name, $properties, $foreign_keys = NULL) {
		$new_props = $this->propsToString($properties);
		$full_string = "CREATE TABLE IF NOT EXISTS $table_name ($new_props";
		if ($foreign_keys != NULL) {
			$full_string .= $this->foreignKeyToString($foreign_keys);
		}
		$full_string .= ');';
		return $full_string;
	}
}

