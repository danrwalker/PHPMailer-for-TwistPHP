<?php


	Twist::framework()->package()->uninstall();

	//Optional Line: Add this line if you are removing all package settings
	Twist::framework()->package()->removeSettings();

	\Twist::framework()->hooks()->cancel('TWIST_EMAIL_PROTOCOLS','phpmailer-email-protocol',true);