<?php
/**
 * @package eabWhois
 * @class   eabWhois
 * @author  Andy Caiger <acaiger@eab.co.uk>
 * @date    28 March 2014
 **/

class eabWhois
{
	private $cacheSettings;

	public function __construct()
	{
		$this->cacheSettings = array(
			'path' => eZSys::cacheDirectory() . '/eabwhois/',
			'ttl'  => 7200
		);
	}

	public function clearCache( $domain = '' )
	{
		if ( $domain ) {
			$cacheFileHandler = eZClusterFileHandler::instance( $this->cacheSettings['path'] . $domain . '.php' );

			if ( $cacheFileHandler->fileExists( $cacheFileHandler->filePath )) {
				$cacheFileHandler->fileDelete( $cacheFileHandler->filePath );
				eZDebug::writeDebug( 'Deleted "' . $domain . '"entry from cache.', 'EAB Whois' );
				eZLog::write ( 'Deleted "' . $domain . '" entry from cache.', 'whois.log');
			}
		}
	}

	public function getInfo( $domain = '' )
	{
		$cacheFileHandler = eZClusterFileHandler::instance( $this->cacheSettings['path'] . $domain . '.php' );

		try {
			if ($cacheFileHandler->fileExists( $cacheFileHandler->filePath ) === false || time() > ( $cacheFileHandler->mtime() + $this->cacheSettings['ttl'] ))
			{
				eZDebug::writeDebug( 'Looking up "' . $domain . '".', 'EAB Whois' );
				eZLog::write ( 'Looking up "' . $domain . '".', 'whois.log');

				$info = ezjscServerFunctionsWhois::info( array( $domain ));

				$cacheFileHandler->fileStoreContents( $cacheFileHandler->filePath, serialize( $info ) );
			}
			else
			{
				eZDebug::writeDebug( 'Retrieving "' . $domain . '" from cache.', 'EAB Whois' );
				eZLog::write ( 'Retrieving "' . $domain . '" from cache.', 'whois.log');
				$info = unserialize( $cacheFileHandler->fetchContents() );
			}

			return array( 'result' => $info );
		} catch( Exception $e ) {
			eZDebug::writeError( $e, 'EAB Whois' );
			return false;
		}
	}

}

?>
