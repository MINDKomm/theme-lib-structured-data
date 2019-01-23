<?php

namespace Theme\Structured_Data;

use Spatie\SchemaOrg\Schema;

/**
 * Class Social_Profile
 *
 * @see https://developers.google.com/search/docs/data-types/social-profile
 *
 * Add Structured Data in the JSON-LD format.
 * @see https://developers.google.com/search/docs/guides/intro-structured-data
 *
 * To test your Structured Data, you can use the following Testing Tool:
 * https://search.google.com/structured-data/testing-tool.
 *
 * @since 2.0.0
 */
class Social_Profile {
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
		'myspace',
		'pinterest',
		'soundcloud',
		'tumblr',
	];

	/**
	 * Social Profiles.
	 *
	 * @var array An associative array of social profile URLs.
	 */
	protected $social_profile_urls;

	/**
	 * Social_Profile constructor.
	 *
	 * @param array $social_profile_urls An associative array of social profile URLs.
	 */
	public function __construct( $social_profile_urls = [] ) {
		$this->social_profile_urls = $social_profile_urls;
	}

	/**
	 * Get array of social profile links.
	 *
	 * @return array
	 */
	private function get_social_profiles() {
		$profiles = [];

		if ( ! empty( $this->social_profile_urls ) ) {
			foreach ( self::$social_profiles as $name ) {
				if ( ! empty( $this->social_profile_urls[ $name ] ) ) {
					$profiles[] = $this->social_profile_urls[ $name ];
				}
			}
		}

		return $profiles;
	}

	/**
	 * Generate JSON-LD string to be appended to document head.
	 *
	 * @return string The JSON-LD output.
	 */
	public function generate_jsonld() {
		$social_profiles = $this->get_social_profiles();

		if ( empty( $social_profiles ) ) {
			return '';
		}

		$organization = Schema::organization()
			->sameAs( $social_profiles );

		return $organization->toScript();
	}
}
