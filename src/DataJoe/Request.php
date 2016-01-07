<?php

namespace DataJoe;

class Request {

	public function __construct($projectId, $params = [])
	{
		// add given parameters to the defaults
		$this->params = array_merge([
			'API' => 'project',
			'rows' => '1|1000',
			'return_type' => 'JSON',
			//'year' => $year
			//'djoResetCache' => 'all',
			'djoResetCache' => 'true',
			'include_hidden_fields' => 'true',
			'P' => $projectId
        ], $params);

		$this->endpoint = env('APP_ENV') == 'local' ? 'http://www.arkansasbride.com/datajoeProxy.php' : 'https://secure.datajoe.com/api/';

		$this->params['api_key'] = env('DATAJOE_KEY');

    	$this->requestUrl = $this->buildRequestUrl();
	}

	public function buildRequestUrl()
	{
		return $this->endpoint . '?' . http_build_query($this->params);
	}

	public function getJson()
	{
		return file_get_contents($this->requestUrl);
	}

	public function get()
	{
		$json = $this->getJson();

		return json_decode($json);
	}

	public function getHeader()
	{
		$response = $this->get();

		return $response->HEADER;
	}

}