eabwhois
========

eZ Publish legacy extension providing an interface to whois servers.

##Summary

Provides a template fetch function and an Ajax call to interface with various whois servers.

##Requirements

This version requires eZ Publish 4 or eZ Publish 5 Legacy Edition.
It requires two libraries for parsing text from whois servers:

* [WhoisParser](https://github.com/novutec/WhoisParser)
* [DomainParser](https://github.com/novutec/DomainParser)

It also requires the following eZ Publish legacy extensions:

* ezjscore

##Copyright

eZ Publish wrapper copyright 2012-2014 [Enterprise AB Ltd](http://eab.uk).

Parser libraries:

* For WhoisParser see [classes/WhoisParser/README.md](classes/WhoisParser/README.md)
* For DomainParser see [classes/DomainParser/README.md](classes/DomainParser/README.md)

##License

eZ Publish wrapper licensed GPL.

Parser libraries:

* For WhoisParser see [classes/WhoisParser/README.md](classes/WhoisParser/README.md)
* For DomainParser see [classes/DomainParser/README.md](classes/DomainParser/README.md)

##Install

1. Copy the `eabwhois` folder to the `extension` folder.

2. Edit `settings/override/site.ini.append.php`

3. Under `[ExtensionSettings]` add:
        ActiveExtensions[]=eabwhois

4. Clear the cache:
        bin/php/ezcache.php --clear-all

##Usage

Use of fetch:

	$whois = fetch( 'whois', 'info', hash( 'domain', $domain_name ))
	<h2>Information held at the Registrar</h2>
	{if $whois.expiry_date}<p><b>Expiry date:</b> {$whois.expiry_date|wash}</p>{/if}
	{if $whois.registrar}<p><b>Registrar:</b> {$whois.registrar|wash}</p>{/if}
	{if $whois.owner}<p><b>Owner:</b> {$whois.owner|wash}</p>{/if}
	{if $whois.address}<p><b>Address:</b> {$whois.address|wash}</p>{/if}
	{if $whois.warning}
	<h3>Warning</h3>
	<p>{$whois.warning}</p>
	{/if}

Example Ajax call to write information into an element `<div id="whoisfeedback">`:

	$.ez(  'ezjscwhois::info::'+domain, {http_accept: 'json'}, function( ezp_data ) {
		if ( ezp_data.error_text )  
		{
			alert( ezp_data.error_text );
		}
		else
		{
			$("#whoisfeedback").html( "<h2>Domain details:</h2>" );
			
			if (ezp_data.content.owner)
				$("#whoisfeedback").append( "<p><b>Owner:</b> " + ezp_data.content.owner + "</p>" );
			else
				$("#whoisfeedback").append( "<p><b>Owner:</b> unknown</p>" );

			if (ezp_data.content.warning)
				$("#whoisfeedback").append( '<p><b>Warning:</b> ' + ezp_data.content.warning + '</p>' );
		}
	}
