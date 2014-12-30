<?php

/* Simple PHP script to test the Whois Parser.
You will need to add the following to .htaccess to run it:

# Uncomment to enable testing of the Whois Parser
RewriteRule ^extension/eabwhois/classes/test\.php - [L]

TODO:

Need to find/write templates for:

whois.123-reg.co.uk

*/

function wash($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

?>

<html>
<head>
	<title>Testing Whois Parser</title>
</head>
<body>

<h1>Testing Whois Parser</h1>

<?

if (empty( $_POST["domain"]) )
{
	$domain = "";
}
else
{
	$domain = wash( $_POST["domain"] );

	if (!empty( $domain ))
	{
		require_once 'DomainParser/Parser.php';
		require_once 'WhoisParser/Parser.php';

		$Parser = new Novutec\WhoisParser\Parser();
		$Parser->setDateFormat( '%d/%m/%Y' );
		$result = $Parser->lookup($domain);

		echo "<h1>Results for " . $domain . "</h1>";

		echo "<b>Domain:</b> " . $result->name . "<br />";

		echo "<b>Expiry date:</b> ";
		if ($result->expires)
			echo $result->expires . "<br />";
		else
			"Unknown <br />";

		if ($result->registrar)
		{
			echo "<b>Registrar:</b> ";
			if ( $result->registrar->name == "Enterprise AB Ltd t/a EAB" )
				echo "Nominet";
			else
				echo $result->registrar->name;
			echo "<br />";
		}
		else
		{
			echo "<b style=\"color:#FF0000\">Warning:</b> can't identifer registrar<br />";
			echo "<pre>";
			print_r($result);
			echo "</pre>";
		}

		if ($result->exception)
		{
			echo "<b style=\"color:#FF0000\">Warning:</b> " . $result->exception . "<br />";
			echo "<pre>";
			print_r($result);
			echo "</pre>";
		}

		/* echo "<pre>";
		print_r($result);
		echo "</pre>"; */

		$owners = $result->contacts->owner;
		if (isset( $owners))
			foreach ($owners as $owner)
			{
				echo "<b>Owner:</b> " . $owner->name . "<br/>";
				if ($owner->organization) echo "<b>Organization:</b> " . $owner->organization . "<br/>";
				echo "<b>Address:</b> ";
				if ($owner->address)
				{
					if ($owner->address) echo $owner->address . ", ";
					if ($owner->city) echo $owner->city . ", ";
					if ($owner->state) echo $owner->state . ", ";
					if ($owner->zipcode) echo $owner->zipcode . ", ";
					if ($owner->country) echo $owner->country;
					echo "<br/>";
				}
				else
					echo "Not found";
			}
		else
		{
			echo "<b style=\"color:#FF0000\">Warning:</b> Can't identify registered owner";
			echo "<pre>";
			print_r($result);
			echo "</pre>";
		}
		
		if ( preg_match( "/^Template (.+) could not be found.$/i", $result->exception ))
		{
			echo "<pre>";
			foreach ($result->rawdata as $line)
				echo $line . "<br />";
			echo "</pre>";
			echo "<hr />";
		}
	}
}

?>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
	<label>Domain <input type="text" value="<?php echo $domain; ?>" name="domain" /></label>
	<input type="submit" value="Lookup" /> 
</form>

</body>
</html>

