<?php

	namespace Packages\phpmailer\Models;

	class Send{

		public static function protocolName(){
			return 'PHPMailer';
		}

		/**
		 * @param \Twist\Core\Models\Email\Create $resEmail
		 * @return bool
		 * @throws \PHPMailer\PHPMailer\Exception
		 */
		public static function protocolSend($resEmail){

			$blStatus = false;

			$arrSettings = array(
				'host' => \Twist::framework()->setting('PHPMAILER_SMTP_HOST'),
				'port' => \Twist::framework()->setting('PHPMAILER_SMTP_PORT'),
				'security' => \Twist::framework()->setting('PHPMAILER_SMTP_SECURITY'),
				'username' => \Twist::framework()->setting('PHPMAILER_SMTP_USERNAME'),
				'password' => \Twist::framework()->setting('PHPMAILER_SMTP_PASSWORD'),
				'from' => \Twist::framework()->setting('PHPMAILER_SMTP_FROM_ADDRESS')
			);

			if(self::checkCredentials($arrSettings)){

				require_once TWIST_PACKAGES.'/phpmailer/ThirdParty/PHPMailer/src/PHPMailer.php';
				require_once TWIST_PACKAGES.'/phpmailer/ThirdParty/PHPMailer/src/OAuth.php';
				require_once TWIST_PACKAGES.'/phpmailer/ThirdParty/PHPMailer/src/POP3.php';
				require_once TWIST_PACKAGES.'/phpmailer/ThirdParty/PHPMailer/src/SMTP.php';
				require_once TWIST_PACKAGES.'/phpmailer/ThirdParty/PHPMailer/src/Exception.php';

				$arrEmailSource = $resEmail->source();
				$arrEmailData = $resEmail->data();

				$resPHPMailer = new \PHPMailer\PHPMailer\PHPMailer(true);

				try{
					$resPHPMailer->isSMTP();

					$resPHPMailer->Host = $arrSettings['host'];
					$resPHPMailer->Port = $arrSettings['port'];
					$resPHPMailer->SMTPSecure = $arrSettings['security'];

					//If Username and/or Password has been set enable auth
					if($arrSettings['username'] != '' || $arrSettings['password'] != ''){
						$resPHPMailer->SMTPAuth = true;
						$resPHPMailer->Username = $arrSettings['username'];
						$resPHPMailer->Password = $arrSettings['password'];
					}

					$resPHPMailer->SetFrom($arrSettings['from']);
					$resPHPMailer->addReplyTo($arrEmailData['reply_to']);

					if(array_key_exists('to',$arrEmailData) && is_array($arrEmailData['to']) && count($arrEmailData['to']) > 0){
						foreach($arrEmailData['to'] as $strEmailAddress => $strName){
							$resPHPMailer->addAddress($strEmailAddress, $strName);
						}
					}

					if(array_key_exists('cc',$arrEmailData) && is_array($arrEmailData['cc']) && count($arrEmailData['cc']) > 0){
						foreach($arrEmailData['cc'] as $strEmailAddress => $strName){
							$resPHPMailer->addCC($strEmailAddress, $strName);
						}
					}

					if(array_key_exists('bcc',$arrEmailData) && is_array($arrEmailData['bcc']) && count($arrEmailData['bcc']) > 0){
						foreach($arrEmailData['bcc'] as $strEmailAddress => $strName){
							$resPHPMailer->addBCC($strEmailAddress, $strName);
						}
					}

					//$resPHPMailer->SMTPDebug  = 3;
					//$resPHPMailer->Debugoutput = function($str, $level) {echo "debug level $level; message: $str";};
					//$resPHPMailer->Debugoutput = 'echo';

					$resPHPMailer->IsHTML(true);

					$resPHPMailer->Subject = $arrEmailData['subject'];
					$resPHPMailer->Body = $arrEmailData['body_html'];
					$resPHPMailer->MsgHTML = $arrEmailData['body_html'];
					$resPHPMailer->AltBody = $arrEmailData['body_plain'];

					if($resPHPMailer->send()){
						$blStatus = true;
					}else{
						$blStatus = false;
					}

				}catch(\PHPMailer\PHPMailer\Exception $resException){

					//Handle the exception here
					echo $resException->getMessage();
					$blStatus = false;
				}
			}

			return $blStatus;
		}

		protected static function checkCredentials($arrSettings){

			//@todo check that all the credentials have been filled in correctly

			return true;
		}
	}