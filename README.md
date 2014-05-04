# NIC.ar support for WHMCS

This project adds some features to support Argetinian TLDs (".ar") to the popular client management, billing and support solution for webhosting resellers know as [WHMCS][whmcs].

## Addon installation

![screenshot](https://raw.github.com/vivaserver/whmcs-nic_ar/master/screenshot-addon.png)

A [WHMCS][whmcs] admin. addon for the [nic!alert API][api] to [NIC.ar][nic]

Just copy the `modules/addons/nic_ar` directory into the `modules/addons` directory of your WHMCS installation.
Later you can enable and configure the addon from the **Setup &gt; Addon Modules** menu option of the WHMCS Admin. interface.

## WHOIS support for ".ar" domains

You can also test for the availabiltity of any ".ar" domain name adding the following lines to the `includes/whoisservers.php` file of your WHMCS installation:

    .com.ar|http://api.nicalert.me/v1/available/|HTTPREQUEST-:true
    .gob.ar|http://api.nicalert.me/v1/available/|HTTPREQUEST-:true
    .int.ar|http://api.nicalert.me/v1/available/|HTTPREQUEST-:true
    .mil.ar|http://api.nicalert.me/v1/available/|HTTPREQUEST-:true
    .net.ar|http://api.nicalert.me/v1/available/|HTTPREQUEST-:true
    .org.ar|http://api.nicalert.me/v1/available/|HTTPREQUEST-:true
    .tur.ar|http://api.nicalert.me/v1/available/|HTTPREQUEST-:true

To enable these domains in your stock WHMCS `/domainchecker.php` page, you have to explicitly add your selection of supported domains in the **Setup &gt; Product/Services &gt; Domain Pricing** menu option of the Admin. interface.
Do not forget to assing a **pricing** to each, or else they will *not* show up in your `/domainchecker.php` page.

![screenshot](https://raw.github.com/vivaserver/whmcs-nic_ar/master/screenshot-whois.png)

Also, please note that this service will not produce accurate results when it's overloaded by requests; to overcome this limitation, please check the [nic!alert API pricing][price].

## WHMCS version support

Versions 5.3.5 and 5.3.6 were tested OK against these features. Other versions might work too. Please open an [issue][issue] to help test compatibility.

## License

MIT

## Copyright

(c)2014 Cristian R. Arroyo

[nic]: http://www.nic.ar
[api]: http://api.nicalert.me
[price]: http://api.nicalert.me/pricing
[issue]: https://github.com/vivaserver/whmcs-nic_ar/issues/new
[whmcs]: http://www.whmcs.com
