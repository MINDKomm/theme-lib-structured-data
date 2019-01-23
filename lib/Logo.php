<?php

namespace Theme\Structured_Data;

use Spatie\SchemaOrg\Schema;

/**
 * Class Logo
 *
 * @see https://developers.google.com/search/docs/data-types/logo
 *
 * Add Structured Data in the JSON-LD format.
 * @see https://developers.google.com/search/docs/guides/intro-structured-data
 *
 * To test your Structured Data, you can use the following Testing Tool:
 * https://search.google.com/structured-data/testing-tool.
 */
class Logo {
	/**
	 * Logo URL.
	 *
	 * @var string The URL to the logo.
	 */
	protected $logo_url;

	/**
	 * Logo constructor.
	 *
	 * @param string $logo_url The URL to the logo.
	 */
	public function __construct( $logo_url ) {
		// Convert logo into URL if we have a potential ID.
		if ( is_numeric( $logo_url ) ) {
			$logo_url = wp_get_attachment_image_src( $logo_url, 'full' )[0];
		}

		$this->logo_url = $logo_url;
	}

	/**
	 * Generate JSON-LD string to be appended to document head.
	 *
	 * @return string The JSON-LD output.
	 */
	public function generate_jsonld() {
		$website = Schema::organization()
			->logo( $this->logo_url )
			->url( home_url() );

		return $website->toScript();
	}
}
