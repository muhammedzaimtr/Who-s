<!DOCTYPE html>
<html>
<head>
<title>Bu Alan Adı Kimin ?</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<link rel="stylesheet" href="style.css" type="text/css" media="all" />
</head>
<body>
<h2>Bu Alan Adı Kimin ?</h2>
<?php 

/* Muhammed zaim*/


$definitions = array();
$servers = file("whois-lookup-list.txt");


foreach($servers as $server)
{
list($dot,$whois) = explode(" = ", trim($server));
$definitions[$dot] = array(explode(", ", $whois));
}

function printForm()
{
global $keyword,$ext,$definitions;

$action = htmlspecialchars($_SERVER["PHP_SELF"], ENT_QUOTES);
$keyword = str_replace(" src", "", strtolower($keyword));

print <<<ENDHTM
<form method="post" action="$action">
<p><input type="text" name="keyword" value="$keyword" /></p>
<p><select name="ext">
ENDHTM;

foreach($definitions as $key => $value)
{
	if($key == $ext)
	{
	print "<option value=\"$key\" selected=\"selected\">.$key</option>\n";
	}
	else
	{
	print "<option value=\"$key\">.$key</option>\n";
	}
}/* Muhammed zaim*/

print <<<ENDHTM
</select></p>

<p><input type="submit" value="ALAN ADI KİMİN" /></p>
</form>
ENDHTM;
}

if(isset($_POST['keyword']) && strlen($_POST['keyword']) > 0)
{
$keyword = $_POST['keyword'];
$ext = $_POST['ext'];

$keyword = preg_replace('/[^0-9a-zA-Z\-]/','', $keyword);

	if(strlen($keyword) < 2)
	{
	print "<p class=\"error\">Hata: Kelimeniz \"$keyword\" Kısa.</p>\n";
	printForm();
	exit(print "</body></html>\n");
	}
	if(strlen($keyword) > 63)
	{
	print "<p class=\"error\">Hata: Aramak istediğiniz karakter uzun. Maksimum 63 karakterle arama yapabirlirsiniz. Siz ". strlen($keyword) ." Karakter.</p>\n";
	printForm();
	exit(print "</body></html>\n");
	}
	if(!preg_match("/^[a-zA-Z0-9\-]+$/", $keyword))
	{
	print "<p class=\"error\">Hata: Desteklenmeyen Karakterler Kullanıldı.</p>\n";
	printForm();
	exit(print "</body></html>\n");
	}
	if(preg_match("/^-|-$/", $keyword))
	{/* Muhammed zaim*/
	print "<p class=\"error\">Hata: Kelimenin sonunda özel karakter kullanmazsınız.</p>\n";
	printForm();
	exit(print "</body></html>\n");
	}

	printForm();

	$server = $definitions[$ext][0][0];

	if(!$server_conn = @fsockopen($server, 43))
	{
		if(isset($definitions[$ext][0][1]))
		{
		$server = $definitions[$ext][0][1];
		
			if(!$server_conn = @fsockopen($server, 43))
			{
			print "<p class=\"error\">Hata: 	widthhois Sunucusuna Bağlanamadı: ". $definitions[$ext][0][0] ."/". $definitions[$ext][0][1]. "</p>\n";
			exit(print "</body></html>\n");
			}
		}
		else
		{
		print "<p class=\"error\">Hata: Whois Sunucusuna Bağlanamadı: ". $definitions[$ext][0][0] . "</p>\n";
		exit(print "</body></html>\n");
		}
	}

	if($ext=="com" || $ext=="net")
	{
	fputs($server_conn, "$keyword.$ext\n");

		while(!feof($server_conn))
		{
		$temp = fgets($server_conn,128);/* Muhammed zaim*/

			if(preg_match("/Whois Sunucusu:/", $temp))
			{
			$server = str_replace("Whois Sunucusu: ", "", $temp);
			$server = trim($server);
			}
		}

	fclose($server_conn);

		if(!$server_conn = @fsockopen($server, 43))
		{
		print "<p class=\"error\">Hata: Sunucuya Bağlanamadı ". $server . "</p>\n";
		exit(print "</body></html>\n");
		}
	}

$response = "";

fputs($server_conn, "$keyword.$ext\r\n");

	while(!feof($server_conn))
	{
	$response .= fgets($server_conn, 128);
	}

fclose($server_conn);

print "<p>$keyword.$ext İçin ALan Adi Bilgileri</p>\n";

print "<pre>\n";

$response = explode("\n", $response);
/* Muhammed zaim*/
	foreach($response as $line)
	{
	print "$line<br />\n";
	}

print "</pre>";
}
else
{
printForm();
}

?>

</body>
</html>
