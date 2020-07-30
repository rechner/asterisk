<?php


$bootstrap_settings['freepbx_auth']=false;
include '/etc/freepbx.conf';
error_reporting(E_ALL);
#set_error_handler(null);
#set_exception_handler(null);

// Change this.
$asteriskip = "10.0.20.10";

if (isset($_REQUEST['ext'])) {
	$ext = $_REQUEST['ext'];
} else {
	$ext = 300;
}

if (isset($_REQUEST['key'])) {
    if ($_REQUEST['key'] != 'foxydelphia') {
        header("HTTP/1.1 401 Unauthorized");
        exit;
    }
} else {
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

try {

    $user = core_users_get($ext);
    $dev = core_devices_get($ext);

     $xml =  "<?xml version='1.0' encoding='utf-8'?><AccountConfig version='1'><Account><RegisterServer>$asteriskip</RegisterServer>";
     $xml .= "<OutboundServer></OutboundServer><UserID>$ext</UserID><AuthID>$ext</AuthID><AuthPass>".$dev['secret']."</AuthPass>";
     $xml .= "<AccountName>$ext@knot.space</AccountName><DisplayName>".$user['name']."</DisplayName><DNSMode>SRV</DNSMode>";
     $xml .= '<Dialplan>{x+|*x+|*++}</Dialplan><RandomPort>0</RandomPort><SecOutboundServer></SecOutboundServer><Voicemail>*97</Voicemail></Account></AccountConfig>';

    /*
    # This is dumb https://github.com/fusionpbx/fusionpbx/blob/master/resources/templates/provision/grandstream/gswave/%7B%24mac%7D.xml
    #$xml = "<xml version='1.0' encoding='utf-8'?><gs_provision version='1'><config version='1'><P271>1</P271><P47>knot.space</P47>";
    #$xml .= "<P270>".$user['name']."</P270><P36>$ext</P36><P35>$ext</P35><P34>".$dev['secret']."</P34><P33>*97</P33><P3>".$user['name']."</P3>";
    #$xml .= "<P103>1</P103></config></gs_provision>";
    */

} catch (Exception $e) {
    header("HTTP/1.1 500 Server Error");
    exit;
}

header("Content-Type: image/png");
include 'phpqrcode/phpqrcode.php';
QRcode::png($xml);
