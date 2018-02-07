<?php

namespace Theme\Structured_Data;

use Spatie\SchemaOrg\GeoCoordinates;
use Spatie\SchemaOrg\LocalBusiness;
use Spatie\SchemaOrg\PostalAddress;
use Spatie\SchemaOrg\Schema;

/**
 * Class Local_Business
 *
 * @package Theme\Structured_Data
 *
 * @see https://developers.google.com/search/docs/data-types/local-businesses
 *
 * Add Structured Data in the JSON-LD format.
 * @see https://developers.google.com/search/docs/guides/intro-structured-data
 *
 * To test your Structured Data, you can use the following Testing Tool:
 * https://search.google.com/structured-data/testing-tool.
 */
class Local_Business {
	/**
	 * Site-wide options provided through ACF fields.
	 *
	 * @var array
	 */
	public $options;

	/**
	 * Possible social profile fields.
	 *
	 * @see https://developers.google.com/search/docs/data-types/social-profile-links
	 *
	 * @var array
	 */
	public static $social_profiles = [
		'facebook',
		'twitter',
		'googleplus',
		'instagram',
		'youtube',
		'linkedin',
		'pinterest',
		'soundcloud',
		'tumblr',
	];

	public function __construct( $options ) {
		$this->options = $options;
	}

	/**
	 * Get array of social profile links.
	 *
	 * @return array
	 */
	private function get_social_profiles() {
		$profiles = [];

		foreach ( self::$social_profiles as $name ) {
			if ( ! empty( $this->options[ 'social_profile_' . $name ] ) ) {
				$profiles[] = $this->options[ 'social_profile_' . $name ];
			}
		}

		return $profiles;
	}

	/**
	 * Get opening hours for local business.
	 *
	 * @see https://schema.org/OpeningHoursSpecification
	 * @see https://developers.google.com/search/docs/data-types/local-businesses#business_hours
	 *
	 * @return array
	 */
	private function get_opening_hours() {
		$opening_hours = [];

		if ( empty( $this->options['opening_hours'] ) ) {
			return $opening_hours;
		}

		foreach ( $this->options['opening_hours'] as $opening_hour ) {
			$schema = Schema::openingHoursSpecification()
				->opens( $opening_hour['opens'] )
				->closes( $opening_hour['closes'] )
				->dayOfWeek( $opening_hour['weekday'] );

			$opening_hours[] = $schema;
		}

		if ( empty( $this->options['opening_hours_closed'] ) ) {
			return $opening_hours;
		}

		foreach ( $this->options['opening_hours_closed'] as $opening_hour ) {
			if ( empty( $opening_hour['date_to'] ) ) {
				$opening_hour['date_to'] = $opening_hour['date_from'];
			}

			$schema = Schema::openingHoursSpecification()
				->opens( '00:00' )
				->closes( '00:00' )
				->validFrom( $opening_hour['date_from'] )
				->validThrough( $opening_hour['date_to'] );

			$opening_hours[] = $schema;
		}

		return $opening_hours;
	}

	/**
	 * Generate JSON-LD string to be appended to document head.
	 *
	 * @return string The JSON-LD output.
	 */
	public function generate_jsonld() {
		$social_profiles = $this->get_social_profiles();
		$opening_hours   = $this->get_opening_hours();
		$options         = $this->options;

		$local_business = Schema::localBusiness()
			->name( ! empty( $options['name'] ) ? $options['name'] : get_option( 'blogname' ) )
			->url( home_url() )
			->description( get_bloginfo( 'description' ) )
			->address( Schema::postalAddress()
				->addressLocality( $options['city'] )
				->postalCode( $options['zip'] )
				->if( ! empty( $options['street'] ), function( PostalAddress $schema ) use ( $options ) {
					$schema->streetAddress( $options['street'] );
				} )
				->if( ! empty( $options['country_code'] ), function( PostalAddress $schema ) use ( $options ) {
					$schema->addressCountry( ! empty( $options['country_code'] ) ? $options['country_code'] : '' );
				} )
				->if( ! empty( $options['post_office_box_number'] ), function( PostalAddress $schema ) use ( $options ) {
					$schema->postOfficeBoxNumber( $options['post_office_box_number'] );
				} )
			)
			->if( ! empty( $options['email'] ), function( LocalBusiness $schema ) use ( $options ) {
				$schema->email( $options['email'] );
			} )
			->if( ! empty( $options['phone'] ), function( LocalBusiness $schema ) use ( $options ) {
				$schema->telephone( $options['phone'] );
			} )
			->if( ! empty( $options['logo'] ), function( LocalBusiness $schema ) use ( $options ) {
				$logo = $options['logo'];

				// Convert logo into URL if we have a potential ID
				if ( is_numeric( $logo ) ) {
					$logo = wp_get_attachment_image_src( $logo, 'full' )[0];
				}

				$schema
					->logo( $logo )
					->image( $logo );
			} )
			->if( ! empty( $options['geo'] ) && ! empty( $options['geo']['geo'] ), function( LocalBusiness $schema ) use ( $options ) {
				$props = $options['geo'];

				$schema->setProperty( 'geo', Schema::geoCoordinates()
					->address( $props['address'] )
					->latitude( $props['lat'] )
					->longitude( $props['lng'] )
					->if( ! empty( $options['zip'] ), function( GeoCoordinates $schema ) use ( $options ) {
						$schema->postalCode( $options['zip'] );
					} )
				);
			} )
			->if( ! empty( $social_profiles ), function( LocalBusiness $schema ) use ( $social_profiles ) {
				$schema->sameAs( $social_profiles );
			} )
			->if( ! empty( $opening_hours ), function( LocalBusiness $schema ) use ( $opening_hours ) {
				$schema->setProperty( 'openingHoursSpecification', $opening_hours );
			} );

		return $local_business->toScript();
	}
}
