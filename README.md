# /r/saskatoon COVID-19 Infographic
This script retrieves COVID-19 dashboard information from the Government of Saskatchewan API, parses it, and displays it in a simple and clean infographic.

## Requirements
* PHP, tested and verified on 7.4.3 but it should work on earlier versions
* php-gd

## Installation
Installation is as simple as installing the dependencies and placing the files in this project in the same directory somewhere on your webserver. No configuration necessary.

## Usage
Once properly installed, all you should need to do is call infographic.php in your web browser. NOTE: The script looks for information for today, if the government has not yet released today's numbers values will instead read "N/A". If this happens to you, simply wait until the government releases numbers for today.

## Donations
If you like this infographic, feel free to buy me a beer. Donations are never mandatory or even expected but are always appreciated.

<a href="https://www.paypal.me/skyline969"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" alt="Donate"/></a>
