<?php

// BLUF 4.5 Postmark integration library

require_once('core/config.php') ; // constants
require_once('vendor/autoload.php') ;
require_once('KEYSpostmark.php') ;

use Postmark\PostmarkClient;

function sendBLUFtransactional($address, $language, $template, $data, $application = false, $reply = false)
{
	// $address = recipient address
	// $language = two letter language code
	// $template = name of the transactional template to use; filename will be json-NAME.tpl
	// $data = array of names and values to insert into template
	// $application = true if this is to be sent from the application server, default = main
	// $reply = mailbox hash to be used for routing replies

	global $bluf ; // our instance of Smarty

	$extratemplates = array('msgnotify','msgforward') ; // additional templates

	BLUFLOG_POSTMARK && openlog('POSTMARK', LOG_ODELAY, LOG_LOCAL3) ;
	BLUFLOG_POSTMARK && syslog(LOG_NOTICE, sprintf("Sending template %s to %s, language is %s", $template, $address, $language)) ;

	if (is_array($data)) {
		foreach ($data as $name => $value) {
			$bluf->assign($name, $value) ;
		}
	}

	$bluf->configLoad('postmark_texts.txt', $language) ;

	$json = $bluf->fetch('postmark/json-' . $template . '.tpl') ;
	$templatedata = json_decode($json) ;

	$sender = ($application) ? APPLICATION_EMAIL . '@' . EMAIL_DOMAIN : NOTIFICATION_EMAIL . '@' . EMAIL_DOMAIN ;

	if ($reply === false) {
		$reply = null ;
	} else {
		$reply = ($application === false) ? 'inbox+' . $reply . '@' . POSTMARK_DOMAIN : APPLICATION_EMAIL . '+' . $reply .'@' . EMAIL_DOMAIN  ;
	}

	// for some functions, use a different template
	$templatename = (in_array($template, $extratemplates)) ? $template : 'generic-transaction' ;

	$serverkey = ($application == true) ? KEY_POSTMARK_JOIN : KEY_POSTMARK ;


	$client = new PostmarkClient($serverkey);

	try {
		$sendResult = $client->sendEmailWithTemplate(
			$sender,
			$address,
			$templatename,
			$templatedata,
			true, // Inline CSS
		  $template . '-' . $language, // Tag
		  null, // Track opens
		  $reply, // Reply To
		  null, // CC
		  null, // BCC
		  null, // Header array
		  null, // Attachment array
		  null, // Track links
		  null, // Metadata array
		  'outbound' // Message stream
		);
	} catch (Exception $e) {
		mail(DEBUG_EMAIL, 'Exception ' . $e->getMessage(), sprintf("Sender: %s\r\nAddress: %s\r\nTemplate name: %s\r\nReply: %s\r\nTemplate: %s\r\nTemplate data: %s\r\nJSON: %s\r\nData: %s\r\n", $sender, $address, $templatename, $reply, $template, print_r($templatedata, true), $json, print_r($data, true))) ;
	}
}
