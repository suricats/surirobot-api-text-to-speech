# Text To speech API

[![Build Status](https://travis-ci.org/suricats/surirobot-api-text-to-speech.svg?branch=master)](https://travis-ci.org/suricats/surirobot-api-text-to-speech)

The goal of this API is to read a text. The API will return the link of the audio which is a .wav

## Code Example

## Requirements

* PHP 7.1
* Composer 

## Installation 

* Clone repository and use public folder as webroot
* Install dependencies
  * `composer install`

* Drop your IBM credentials in resources/credentials/surirobotTTS.json or use the suri-downloader:

  * `cp .env.example .env`
  * `nano .env`

* Fill the login & password fields.
* `tools/get-credentials.sh`

* Make storage/ and public/storage/ writeable by your web server.

## API Reference

This project uses Watson-TextToSpeech from IBM.

## License

This service uses the Lumen framework
The Lumen framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
