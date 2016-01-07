<?php

namespace DataJoe;

use Illuminate\Support\Facades\DB;

class Project {
	
	public function __construct($projectId)
	{
		$this->projectId = $projectId;

		$this->request = new Request($this->projectId);
	}

	public function mapFields($map = [])
	{
		$this->map = $map;
	}

	public  function dumpToTable($table)
	{
		$listings = $this->getListings();

		if (count($listings) > 0) {  
      
			$this->createTable($table);

      		$listings->import($table);
  		}
	}

	public function createTable($table)
	{
		// drop existing table
		DB::statement("drop table if exists `$table`");

		$header = $this->request->getHeader();

		$listings = $this->getListings();

		$fields = [];

		foreach ($listings as $listing) {
        
        	unset($listing->datajoeEntity);
			
			foreach ($listing as $k => $v) {
          		if (isset($fields[$k])):
          			$fields[$k] = strlen(print_r($v, TRUE)) > $fields[$k] ? strlen(print_r($v, TRUE))+100 : $fields[$k];
          		else:
            		$fields[$k] = strlen(print_r($v, TRUE))+100;
          		endif;
        	}

      	}

		$sql = "CREATE TABLE IF NOT EXISTS `{$table}` (
			`id` MEDIUMINT( 10 ) UNSIGNED NOT NULL,
			`name` VARCHAR( 100 ) NOT NULL COMMENT 'Name',\n";

		foreach ($fields as $k => $v) {
			if($k != 'name' && $k != 'id') {
		  		$sql .= "`{$k}` VARCHAR( {$v} ) NOT NULL COMMENT " . str_replace('?', '&#63;', DB::connection()->getPdo()->quote($header->$k->label)) . ",\n";
		  	}
		}

		$sql .= "FULLTEXT KEY `name` (`name`),
			PRIMARY KEY ( `id` )
			) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;";

      	DB::statement($sql);

	}

	public function test()
	{
		return $this->request->getJson();
	}

	public function getListings()
	{
		$responseObject = $this->request->get();

		$listings = Listing::fromDataJoe($responseObject);

		return $listings;
	}
}