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

```php
// Local Business
$local_business = new Theme\Structured_Data\Local_Business( $options );
$local_business->generate_jsonld();

// Website
$website = new Theme\Structured_Data\Website();
$website->generate_jsonld();
```

Put the generated output into the `<head>` of your HTML.

### Website

The class checks if it can find a `search.php` file in your theme directory and will include a [potential search action property](https://developers.google.com/search/docs/data-types/sitelinks-searchbox#modified-type-website) accordingly. If you want to have more control over this, you can pass a boolean to the `generate_jsonld()` function:

```php
// Use SearchAction, ignore check for search.php file.
$website->generate_jsonld( true );
```

### Local Business

A Local Business entry can take a lot of data. The following types of data are supported:

- **Contact Data** – Address, phone number, email address, etc.
- **Social Profiles** – Links to social profiles such as Facebook, Twitter, Instagram.
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
    'social_profile_facebook'   => '',
    'social_profile_twitter'    => 'https://twitter.com/mindkomm',
    'social_profile_googleplus' => '',
    'social_profile_instagram'  => '',
    'social_profile_youtube'    => '',
    'social_profile_linkedin'   => '',
    'social_profile_pinterest'  => '',
    'social_profile_soundcloud' => '',
    'social_profile_tumblr'     => '',
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

// Local Business
$local_business = new Theme\Structured_Data\Local_Business( $options );
$local_business->generate_jsonld();
``` 

- It’s up to you how you want to build this data. We normally use an [ACF Options Page](https://www.advancedcustomfields.com/resources/options-page/) as an interface to edit these values.
- If you don’t provide a value for something, it will be ignored in the output.
- Always check your output with the [Structured Data Testing Tool](https://search.google.com/structured-data/testing-tool/u/0/).

## Credits

Uses the fantastic [schema-org](https://github.com/spatie/schema-org) library by [Spatie](https://spatie.be/en).

## Support

This is a library that we use at MIND to develop WordPress themes. You’re free to use it, but currently, we don’t provide any support. 
