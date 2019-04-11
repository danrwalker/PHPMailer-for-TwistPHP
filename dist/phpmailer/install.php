<?php

	Twist::framework()->package()->install();

	//Optional Line: Add this line if you are adding framework settings
	Twist::framework()->package()->importSettings(sprintf('%s/Data/settings.json',dirname(__FILE__)));

	//Add a new email send protocol to the system
	\Twist::framework()->hooks()->register('TWIST_EMAIL_PROTOCOLS','phpmailer-email-protocol','Packages\phpmailer\Models\Send',true);