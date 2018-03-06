<?php

class CI_DB extends \CI_DB_query_builder {

	/**
	 * Update statement
	 *
	 * Generates a platform-specific update string from the supplied data
	 *
	 * @param	string	the table name
	 * @param	array	the update data
	 * @return	string
	 */
	protected function _update($table, $values)
	{
		foreach ($values as $key => $val)
		{
			$valstr[] = $key.' = '.$val;
		}

		return 'UPDATE '.$table. ' ' . $this->_compile_join() . ' SET '.implode(', ', $valstr)
		.$this->_compile_wh('qb_where')
		.$this->_compile_order_by()
		.($this->qb_limit ? ' LIMIT '.$this->qb_limit : '');
	}

	protected function _compile_join() {
		$sql = '';
		// Write the "JOIN" portion of the query
		if (count($this->qb_join) > 0)
		{
			$sql .= "\n".implode("\n", $this->qb_join);
		}
		return $sql;
	}

}