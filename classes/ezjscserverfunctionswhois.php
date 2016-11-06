<?php
//
// Definition of ezjscServerFunctionsWhois class
//
// Created on: <01-Jun-2010 00:00:00 ls>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Publish Community Project
// SOFTWARE RELEASE:  2012.12
// COPYRIGHT NOTICE: Copyright (C) 1999-2013 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
// 
//   This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
// 
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

/**
 * ezjscServerFunctionsWhois class definition that provide basic whois information about a domain
 *
 */
class ezjscServerFunctionsWhois extends ezjscServerFunctions
{
	/**
	* Returns basic information for given domain
	*
	* a single parameter is supported - the domain:
	* ezjscwhois::info::domain
	*
	* @param mixed $args
	* @return array
	*/
	public static function info( $args )
	{
		$domain = isset( $args[0] ) ? $args[0] : null;

		if ( !$domain )
		{
			throw new ezcBaseFunctionalityNotSupportedException( 'Retrieve basic whois information', 'No domain supplied' );
		}

		if ( !self::validDomain( $domain ))
		{
			throw new ezcBaseFunctionalityNotSupportedException( 'Retrieve basic whois information', 'Invalid domain supplied' );
		}
	
		$Parser = new Novutec\WhoisParser\Parser();
		$Parser->setDateFormat( '%d/%m/%Y' );
		$result = $Parser->lookup( $domain );

		$domainOwner = "";
		$ownerAddress = array();

		if (isset( $result->contacts->owner ))
		{
			foreach ( $result->contacts->owner as $owner )
			{
				$domainOwner = $owner->name;
				if ($owner->organization) $ownerAddress[] = $owner->organization;
				if ($owner->address)
				{
					if ($owner->address) $ownerAddress[] = $owner->address;
					if ($owner->city) $ownerAddress[] = $owner->city;
					if ($owner->state) $ownerAddress[] = $owner->state;
					if ($owner->zipcode) $ownerAddress[] = $owner->zipcode;
					if ($owner->country) $ownerAddress[] = $owner->country;
				}
			}
		}

		$warning = $result->exception;

		if ($result->registrar )
			$registrarName = $result->registrar->name;
		else if ($result->whoisserver == "whois.centralnic.com")
			$registrarName = "CentralNic Ltd";
		else
		{
			$registrarName = null;
			$warning .= "Registrar unknown";
		}
		if ( !$result->expires )
			$warning .= "Expiry date unknown";

		if ( preg_match( '/^Template (.+) could not be found./', $warning ) || !$result->expires || !$registrarName )
		{
			if ( $result->rawdata )
			{
				$warning .= "<pre>";
				foreach ( $result->rawdata as $line )
					$warning .= $line . "<br />";
				$warning .= "</pre>";
			}
		}
		
		return array(
					'expiry_date' => $result->expires,
					'registrar' => $registrarName,
					'owner' => $domainOwner,
					'address' => self::implodeRecursively( $ownerAddress, "," ),
					'warning' => $warning
				);
	}

	function wash($data)
	{
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

	public static function validDomain( $domainName )
	{
		return (preg_match( "/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domainName ));
	}

	static function implodeRecursively( array $array, $glue )
	{
		$result = '';
		foreach( $array as $element ) {
			if ( $result != '' ) {
                            $result .= $glue;
                        }
			if ( is_array( $element ) ) {
				$result .= self::implodeRecursively( $element, $glue );
			} else {
				$result .= $element;
			}
		}
		return $result;
	}
}

?>
