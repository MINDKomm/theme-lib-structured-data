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

		$local_business = Schema::localBusiness();

		$local_business->name( ! empty( $options['name'] )
			? $options['name']
			: get_option( 'blogname' )
		);
		$local_business->url( home_url() );
		$local_business->description( get_bloginfo( 'description' ) );

		$address = Schema::postalAddress();

		if ( ! empty( $options['city'] ) ) {
			$address->addressLocality( $options['city'] );
		}

		if ( ! empty( $options['zip'] ) ) {
			$address->postalCode( $options['zip'] );
		}

		if ( ! empty( $options['street'] ) ) {
			$address->streetAddress( $options['street'] );
		}

		if ( ! empty( $options['country_code'] ) ) {
			$address->addressCountry( ! empty( $options['country_code'] )
				? $options['country_code']
				: ''
			);
		}

		if ( ! empty( $options['post_office_box_number'] ) ) {
			$address->postOfficeBoxNumber( $options['post_office_box_number'] );
		}

		$local_business->address( $address );

		if ( ! empty( $options['email'] ) ) {
			$local_business->email( $options['email'] );
		}

		if ( ! empty( $options['phone'] ) ) {
			$local_business->telephone( $options['phone'] );
		}

		if ( ! empty( $options['logo'] ) ) {
			$logo = $options['logo'];

			// Convert logo into URL if we have a potential ID.
			if ( is_numeric( $logo ) ) {
				$logo = wp_get_attachment_image_src( $logo, 'full' )[0];
			}

			$local_business->logo( $logo )
				->image( $logo );
		}

		if ( ! empty( $options['geo'] ) ) {
			$props = $options['geo'];

			$geo_coordinates = Schema::geoCoordinates();

			$geo_coordinates->address( $props['address'] );
			$geo_coordinates->latitude( $props['lat'] );
			$geo_coordinates->longitude( $props['lng'] );

			if ( ! empty( $options['zip'] ) ) {
				$geo_coordinates->postalCode( $options['zip'] );
			}

			$local_business->setProperty( 'geo', $geo_coordinates );
		}

		if ( ! empty( $social_profiles ) ) {
			$local_business->sameAs( $social_profiles );
		}

		if ( ! empty( $opening_hours ) ) {
			$local_business->setProperty( 'openingHoursSpecification', $opening_hours );
		}

		return $local_business->toScript();
	}
}
