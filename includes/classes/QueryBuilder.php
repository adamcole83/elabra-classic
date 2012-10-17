<?php

/**
 * Class: QueryBuilder
 * A generic SQL query builder.
 */
class QueryBuilder
{

    /**
     * Function: build_select
     * Creates a full SELECT query.
     *
     * Parameters:
     *     $tables - Tables to select from.
     *     $fields - Columns to select.
     *     $order - What to order by.
     *     $limit - Limit of the result.
     *     $offset - Starting point for the result.
     *     $group - What to group by.
     *     $left_join - Any @LEFT JOIN@s to add.
     *
     * Returns:
     *     A @SELECT@ query string.
     */
	public static function build_select($tables,
							   			$fields,
							   			$conds,
							   			$order = null,
							   			$limit = null,
							   			$offset = null,
							   			$group = null,
							   			$left_join = array())
	{
		$query = "SELECT ".$fields.
				 " FROM ".self::build_from($tables);
		
		foreach($left_join as $join)
			$query .= " LEFT JOIN ".$join['table']." ON ".self::build_where($join["where"], $join['table']);
		
		$query .= ($conds ? " WHERE ".self::build_where($conds, $tables) : "").
				  ($group ? " GROUP BY ".self::build_group($group, $tables) : "").
				  ($order ? " ORDER BY ".self::build_order($order, $tables) : "").
				  ($limit ? self::build_limits($offset, $limit) : "");
		
		return $query;
	}
	
    /**
     * Function: build_insert
     * Creates a full insert query.
     *
     * Parameters:
     *     $table - Table to insert into.
     *     $data - Data to insert.
     *
     * Returns:
     *     An @INSERT@ query string.
     */
	public static function build_insert($table, $data)
	{
		return "INSERT INTO {$table} (".
				join(', ', array_keys($data)).
				") VALUES ('".
				join("', '", array_values($data)).
				"')";
	}
	
    /**
     * Function: build_update
     * Creates a full update query.
     *
     * Parameters:
     *     $table - Table to update.
     *     $conds - Conditions to update rows by.
     *     $data - Data to update.
     *
     * Returns:
     *     An @UPDATE@ query string.
     */
	public static function build_update($table, $conds, $data)
	{
		return "UPDATE {$table} SET ".
				join(", ", self::build_params($data)).
				($conds ? " WHERE ".self::build_where($conds) : "");
	}
	
    /**
     * Function: build_delete
     * Creates a full delete query.
     *
     * Parameters:
     *     $table - Table to delete from.
     *     $conds - Conditions to delete by.
     *     &$params - An associative array of parameters used in the query.
     *
     * Returns:
     *     A @DELETE@ query string.
     */
	public static function build_delete($table, $conds)
	{
		return "DELETE FROM {$table} ".
				($conds ? "WHERE ".self::build_where($conds) : "");
	}
	
    /**
     * Function: build_from
     * Creates a FROM header for select queries.
     *
     * Parameters:
     *     $tables - Tables to select from.
     */
    public static function build_from($tables) {
        if (!is_array($tables))
            $tables = array($tables);

        return implode(",\n     ", $tables);
    }
    
    /**
     * Function: build_select_header
     * Creates a SELECT fields header.
     *
     * Parameters:
     *     $fields - Columns to select.
     *     $tables - Tables to tablefy with.
     */
    public static function build_select_header($fields, $tables = null) {
        if (!is_array($fields))
            $fields = array($fields);

        $tables = (array) $tables;

        foreach ($fields as &$field) {
            self::tablefy($field, $tables);
            $field = self::safecol($field);
        }

        return implode(",\n       ", $fields);
    }

    /**
     * Function: safecol
     * Wraps a column in proper escaping if it is a SQL keyword.
     *
     * Doesn't check every keyword, just the common/sensible ones.
     *
     * ...Okay, it only does two. "order" and "group".
     *
     * Parameters:
     *     $name - Name of the column.
     */
    public static function safecol($name) {
        return preg_replace("/(([^a-zA-Z0-9_]|^)(order|group)([^a-zA-Z0-9_]|$))/i", "\\2`\\3`\\4",  $name);
    }
    
    /**
     * Function: build_count
     * Creates a SELECT COUNT(1) query.
     *
     * Parameters:
     *     $tables - Tables to tablefy with.
     *     $conds - Conditions to select by.
     *     &$params - An associative array of parameters used in the query.
     */
    public static function build_count($tables, $conds) {
        return "SELECT COUNT(1) AS count\n".
               "FROM ".self::build_from($tables)."\n".
               ($conds ? "WHERE ".self::build_where($conds) : "");
    }

    /**
     * Function: build_where
     * Creates a WHERE query.
     */
	public static function build_where($conds)
	{	
		return join(" AND ", array_values(self::build_params($conds)));
	}
	
    /**
     * Function: build_params
     * Creates key & value params.
     */
	public static function build_params($data)
	{
		foreach($data as $key => $val)
			$params[] = "{$key} = '{$val}'";
		
		return $params;
	}
	

	public static function build_limits($offset, $limit)
	{
		if($limit === null)
			return "";
		
		if($offset !== null)
			return " LIMIT ".$offset.", ".$limit;
		
		return " LIMIT ".$limit;
	}

    /**
     * Function: build_group
     * Creates a GROUP BY argument.
     *
     * Parameters:
     *     $order - Columns to group by.
     *     $tables - Tables to tablefy with.
     */
    public static function build_group($by, $tables = null) {
        $by = (array) $by;
        $tables = (array) $tables;

        foreach ($by as &$column) {
            self::tablefy($column, $tables);
            $column = self::safecol($column);
        }

        return implode(",\n         ", array_unique(array_filter($by)));
    }
    
    /**
     * Function: build_order
     * Creates an ORDER BY argument.
     *
     * Parameters:
     *     $order - Columns to order by.
     *     $tables - Tables to tablefy with.
     */
    public static function build_order($order, $tables = null) {
        $tables = (array) $tables;

        if (!is_array($order))
            $order = comma_sep($order);

        foreach ($order as &$by) {
            self::tablefy($by, $tables);
            $by = self::safecol($by);
        }

        return implode(",\n         ", $order);
    }

    
    /**
     * Function: tablefy
     * Automatically prepends tables and table prefixes to a field if it doesn't already have them.
     *
     * Parameters:
     *     &$field - The field to "tablefy".
     *     $tables - An array of tables. The first one will be used for prepending.
     */
    public static function tablefy(&$field, $tables) {
        if (!preg_match_all("/(\(|[\s]+|^)([a-z0-9_\.\*]+)(\)|[\s]+|$)/", $field, $matches))
            return $field = str_replace("`", "", $field); # Method for bypassing the prefixer.

        foreach ($matches[0] as $index => $full) {
            $before = $matches[1][$index];
            $name   = $matches[2][$index];
            $after  = $matches[3][$index];

            if (is_numeric($name))
                continue;

            # Does it not already have a table specified?
            if (!substr_count($full, ".")) {
                                       # Don't replace things that are already either prefixed or paramized.
                $field = preg_replace("/([^\.:'\"_]|^)".preg_quote($full, "/")."/",
                                      "\\1".$before."".$tables[0].".".$name.$after,
                                      $field,
                                      1);
            } else {
                # Okay, it does, but is the table prefixed?
                if (substr($full, 0, 2) != "__") {
                                           # Don't replace things that are already either prefixed or paramized.
                    $field = preg_replace("/([^\.:'\"_]|^)".preg_quote($full, "/")."/",
                                          "\\1".$before."".$name.$after,
                                          $field,
                                          1);
                }
            }
        }

        $field = preg_replace("/AS ([^ ]+)\./i", "AS ", $field);
    }

}

?>