# Page as Post Type Archive Plugin

[![Packagist Version](https://img.shields.io/packagist/v/itinerisltd/page-as-post-type-archive.svg?label=release&style=flat-square)](https://packagist.org/packages/itinerisltd/page-as-post-type-archive)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/itinerisltd/page-as-post-type-archive.svg?style=flat-square)](https://packagist.org/packages/itinerisltd/page-as-post-type-archive)
[![Packagist Downloads](https://img.shields.io/packagist/dt/itinerisltd/page-as-post-type-archive.svg?label=packagist%20downloads&style=flat-square)](https://packagist.org/packages/itinerisltd/page-as-post-type-archive/stats)
[![GitHub License](https://img.shields.io/github/license/itinerisltd/page-as-post-type-archive.svg?style=flat-square)](https://github.com/ItinerisLtd/page-as-post-type-archive/blob/master/LICENSE)
[![Hire Itineris](https://img.shields.io/badge/Hire-Itineris-ff69b4.svg?style=flat-square)](https://www.itineris.co.uk/contact/)
[![Twitter Follow @itineris_ltd](https://img.shields.io/twitter/follow/itineris_ltd?style=flat-square&color=1da1f2)](https://twitter.com/itineris_ltd)

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->

- [Minimum Requirements](#minimum-requirements)
- [Installation](#installation)
    - [Composer (Recommended)](#composer-recommended)
    - [Build from Source (Not Recommended)](#build-from-source-not-recommended)
- [Credits](#credits)
- [License](#license)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->


## Minimum Requirements

- PHP v7.4
- WordPress v5.8
- Bedrock by [roots.io](https://roots.io/)

## Installation

### Composer (Recommended)

```bash
composer require itinerisltd/page-as-post-type-archive
```

### Build from Source (Not Recommended)

```bash
# Make sure you use the same PHP version as remote servers.
# Building inside docker images is recommanded.
php -v

# Checkout source code
git clone https://github.com/ItinerisLtd/page-as-post-type-archive.git
cd page-as-post-type-archive
git checkout <the-tag-or-the-branch-or-the-commit>

# Build the zip file
composer release:build
```

Then, install `release/page-as-post-type-archive.zip` [as usual](https://codex.wordpress.org/Managing_Plugins#Installing_Plugins).

## Credits

[page-as-post-type-archive](https://github.com/ItinerisLtd/page-as-post-type-archive) is a [Itineris Limited](https://www.itineris.co.uk/) project created by [Dan Lapteacru](https://github.com/danlapteacru).

Full list of contributors can be found [here](https://github.com/ItinerisLtd/page-as-post-type-archive/graphs/contributors).

## License

[Page as post type archive](https://github.com/ItinerisLtd/page-as-post-type-archive) is released under the [MIT License](https://opensource.org/licenses/MIT).