<?php

namespace DataJoe;

class Listing {

	public function __construct()
	{

	}

	public static function fromDataJoe($responseObject)
	{
		$listingsArray = $responseObject->BODY;

		$listings = [];

		foreach ($listingsArray as $listing) {
			$listingObject = new Listing();

			$listingObject->datajoeEntity = $listing;

			$listingObject->id = $listing->ID;

			$listingObject->name = trim($listing->NAME);

			foreach ($listing->DATA as $key => $value) {
				$listingObject->$key = trim($value);
			}

			$listings[] = $listingObject;
		}

		return new ListingCollection($listings);
	}

}