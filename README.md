# Structured Data

Structured Data helper classes for WordPress themes.

Supports the following data types:

- [Local Business](https://developers.google.com/search/docs/data-types/local-businesses)
- [Website](https://developers.google.com/search/docs/data-types/sitelinks-searchbox)

## Installation

You can install the package via Composer:

```bash
composer require mindkomm/theme-lib-structured-data
```

## Usage

Put the generated output into the `<head>` of your HTML.

```php
add_action( 'wp_head', function() {
    // Local Business
    $local_business = new Theme\Structured_Data\Local_Business( $options );
    echo $local_business->generate_jsonld();

    // Website
    $website = new Theme\Structured_Data\Website();
    echo $website->generate_jsonld();

    // Logo
    $logo = new Theme\Structured_Data\Logo( $logo_url );
    echo $logo->generate_jsonld();

    // Social profiles
    $social_profiles = new Theme\Structured_Data\Social_Profile( $social_profiles );
    echo $social_profiles->generate_jsonld();
} );
```

### Website

The class checks if it can find a `search.php` file in your theme directory and will include a [potential search action property](https://developers.google.com/search/docs/data-types/sitelinks-searchbox#modified-type-website) accordingly. If you want to have more control over this, you can pass a boolean to the `generate_jsonld()` function:

```php
// Use SearchAction, ignore check for search.php file.
echo $website->generate_jsonld( true );
```

### Local Business

A Local Business entry can take a lot of data. The following types of data are supported:

- **Contact Data** – Address, phone number, email address, etc.
- **Geocodes** – The latitude and longitude of where the business is located on the map.
- **Opening hours** – The opening hours of a business.

You need to pass this data when you instantiate the class in the form of an array. Here’s an overview over all the options you can pass in:

```php
<?php

$options = [
    'name'                      => 'MIND Kommunikation GmbH',
    'street'                    => 'Wülflingerstrasse 36',
    'post_office_box_number'    => '',
    'zip'                       => '8400',
    'city'                      => 'Winterthur',
    'email'                     => 'hello@mind.ch',
    'phone'                     => '052 203 45 00',
    'country_code'              => 'CH',
    'logo'                      => 'https://www.mind.ch/our-logo.png',
    'social_profiles' => [
         'facebook'   => '',
         'twitter'    => 'https://twitter.com/mindkomm',
         'instagram'  => '',
         'youtube'    => '',
         'linkedin'   => '',
         'myspace'    => '',
         'pinterest'  => '',
         'soundcloud' => '',
         'tumblr'     => '',
     ],
    'location_map_url'          => 'https://www.google.ch/maps/place/MIND+Kommunikation+GmbH/@47.5054599,8.715229,17z/data=!3m1!4b1!4m5!3m4!1s0x479a999cb35b1801:0xefef1560f7750a4f!8m2!3d47.5054599!4d8.7174177',
    'geo'                       => [
        'address' => 'MIND Kommunikation GmbH, Wülflingerstrasse, Winterthur, Schweiz',
        'lat'     => '47.50545990000001',
        'lng'     => '8.717417699999942',
    ],
    'opening_hours'             => [
        [
            'weekday' => 'Monday',
            'opens'   => '08:00:00',
            'closes'  => '17:30:00',
        ],
        [
            'weekday' => 'Tuesday',
            'opens'   => '08:00:00',
            'closes'  => '17:30:00',
        ],
        [
            'weekday' => 'Wednesday',
            'opens'   => '08:00:00',
            'closes'  => '17:30:00',
        ],
        // …
    ],
    'opening_hours_closed'      => [
        [
            'date_from' => '2018-12-24',
            'date_to'   => '2019-01-02',
        ],
    ],
];

$local_business = new Theme\Structured_Data\Local_Business( $options );

echo $local_business->generate_jsonld();
``` 

### Logo

Adds logo markup according to the Structured Data Guide for [Logo](https://developers.google.com/search/docs/data-types/logo).

```php
$logo = new Theme\Structured_Data\Logo( 'https://www.mind.ch/our-logo.png' );

echo $logo->generate_jsonld();
```

### Social Profiles

Links to social profiles such as Facebook, Twitter, Instagram according to the Structured Data Guide for [Social Profile](https://developers.google.com/search/docs/data-types/social-profile).

```php
$social_profiles = new Theme\Structured_Data\Social_Profile( [
    'facebook'   => '',
    'twitter'    => 'https://twitter.com/mindkomm',
    'instagram'  => '',
    'youtube'    => '',
    'linkedin'   => '',
    'myspace'    => '',
    'pinterest'  => '',
    'soundcloud' => '',
    'tumblr'     => '',
] );

echo $social_profiles->generate_jsonld();
```

### Usage remarks

- It’s up to you how you want to build this data. You can set up a customizer page or you could use an [ACF Options Page](https://www.advancedcustomfields.com/resources/options-page/) as an interface to edit these values.
- If you don’t provide a value for something, it will be ignored in the output.
- Always check your output with the [Structured Data Testing Tool](https://search.google.com/structured-data/testing-tool/u/0/).

## Credits

Uses the fantastic [schema-org](https://github.com/spatie/schema-org) library by [Spatie](https://spatie.be/en).

## Support

This is a library that we use at MIND to develop WordPress themes. You’re free to use it, but currently, we don’t provide any support. 
