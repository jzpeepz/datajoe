<?php

namespace DataJoe;

use Illuminate\Support\Facades\DB;

class ListingCollection implements \Iterator {

	protected $cursor = 0;

    private $entries = array();

    public function __construct($entries = array())
    {
        $this->entries = $entries;
    }

    public function toArray()
    {
        return $this->entries;
    }

    public function current()
    {
        return $this->entries[$this->cursor];
    }

    public function next()
    {
        $this->cursor++;
    }

    public function key()
    {
        return $this->cursor;
    }

    public function valid()
    {
        return isset($this->entries[$this->cursor]);
    }

    public function rewind()
    {
        $this->cursor = 0;
    }

    public function import($table)
    {
    	foreach ($this->entries as $listing) {

    		unset($listing->datajoeEntity);

    		if (stripos($listing->name, 'Arkansas Business Publishing Group') === FALSE) {
				$keys = [];
				$values = [];

				foreach ($listing as $key => $value) {
					// $value = $this->sanitize($value);
					$keys[] = $key;
					$values[] = DB::connection()->getPdo()->quote(print_r($value, TRUE));
				}

				$dup = [];
				foreach ($keys as $index => $key) {
					$dup[] = '`'.$key.'`='.$values[$index];
				}

				$sql = 'INSERT INTO `'.$table.'` (`'.join('`, `', $keys).'`) VALUES ('.join(', ', $values).') ON DUPLICATE KEY UPDATE ' . join(', ', $dup);
				
				DB::statement($sql);
    		}
		}
    }
}