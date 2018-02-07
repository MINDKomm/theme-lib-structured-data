<?php

namespace Theme\Structured_Data;

use Spatie\SchemaOrg\Schema;

/**
 * Class Website
 *
 * @package Theme\Structured_Data
 *
 * @see https://developers.google.com/search/docs/data-types/sitelinks-searchbox
 *
 * Add Structured Data in the JSON-LD format.
 * @see https://developers.google.com/search/docs/guides/intro-structured-data
 *
 * To test your Structured Data, you can use the following Testing Tool:
 * https://search.google.com/structured-data/testing-tool.
 */
class Website {
	/**
	 * Generate JSON-LD string to be appended to document head.
	 *
	 * @param bool|string $has_search Whether SearchAction should be included. Will use a boolean
	 *                                if provided. Will include the SearchAction if a `search.php`
	 *                                file can be found in the theme directory by default. Default
	 *                                'unset'.
	 *
	 * @return string The JSON-LD output.
	 */
	public function generate_jsonld( $has_search = 'unset' ) {
		if ( 'unset' === $has_search ) {
			$has_search = file_exists( get_template_directory() . '/search.php' );
		}

		$website = Schema::webSite()
			->name( get_bloginfo( 'name' ) )
			->url( home_url() )
			->if( $has_search, function( \Spatie\SchemaOrg\WebSite $schema ) {
				$schema->potentialAction( Schema::searchAction()
					->setProperty( 'target', home_url() . '/?s={search_term_string}' )
					->setProperty( 'query-input', 'required name=search_term_string' )
				);
			} );

		return $website->toScript();
	}
}

