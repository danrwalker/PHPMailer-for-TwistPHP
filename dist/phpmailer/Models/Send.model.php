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
				'from' => \Twist::framework()->setting('PHPMAILER_SMTP_FROM')
			);

			if(self::checkCredentials($arrSettings)){

				require_once TWIST_PACKAGES.'/phpmailer/ThirdParty/PHPMailer/src/PHPMailer.php';
				require_once TWIST_PACKAGES.'/phpmailer/ThirdParty/PHPMailer/src/OAuth.php';
				require_once TWIST_PACKAGES.'/phpmailer/ThirdParty/PHPMailer/src/POP3.php';
				require_once TWIST_PACKAGES.'/phpmailer/ThirdParty/PHPMailer/src/SMTP.php';
				require_once TWIST_PACKAGES.'/phpmailer/ThirdParty/PHPMailer/src/Exception.php';

				$arrEmailSource = $resEmail->source();

				$resPHPMailer = new \PHPMailer\PHPMailer\PHPMailer(true);

				try{
					$resPHPMailer->isSMTP();

					$resPHPMailer->Host = $arrSettings['host'];
					$resPHPMailer->Port = $arrSettings['port'];
					$resPHPMailer->SMTPSecure = $arrSettings['security'];
					$resPHPMailer->SMTPAuth = true;
					$resPHPMailer->Username = $arrSettings['username'];
					$resPHPMailer->Password = $arrSettings['password'];

					$resPHPMailer->SetFrom($arrSettings['from']);
					$resPHPMailer->addReplyTo($resEmail->arrEmailData['reply_to'], 'Information');

					if(array_key_exists('to',$resEmail->arrEmailData) && is_array($resEmail->arrEmailData['to']) && count($resEmail->arrEmailData['to']) > 0){
						foreach($resEmail->arrEmailData['to'] as $strEmailAddress => $strName){
							$resPHPMailer->addAddress($strEmailAddress, $strName);
						}
					}

					if(array_key_exists('cc',$resEmail->arrEmailData) && is_array($resEmail->arrEmailData['cc']) && count($resEmail->arrEmailData['cc']) > 0){
						foreach($resEmail->arrEmailData['cc'] as $strEmailAddress => $strName){
							$resPHPMailer->addCC($strEmailAddress, $strName);
						}
					}

					if(array_key_exists('bcc',$resEmail->arrEmailData) && is_array($resEmail->arrEmailData['bcc']) && count($resEmail->arrEmailData['bcc']) > 0){
						foreach($resEmail->arrEmailData['bcc'] as $strEmailAddress => $strName){
							$resPHPMailer->addBCC($strEmailAddress, $strName);
						}
					}

					//$resPHPMailer->SMTPDebug  = 3;
					//$resPHPMailer->Debugoutput = function($str, $level) {echo "debug level $level; message: $str";}; $mail->Debugoutput = 'echo';

					$resPHPMailer->IsHTML(true);

					$resPHPMailer->Subject = $resEmail->arrEmailData['subject'];
					$resPHPMailer->Body = $resEmail->arrEmailData['body_html'];
					$resPHPMailer->MsgHTML = $resEmail->arrEmailData['body_html'];
					$resPHPMailer->AltBody = $resEmail->arrEmailData['body_plain'];

					if($resPHPMailer->send()){
						$blStatus = true;
					}else{
						$blStatus = false;
					}

				}catch(\PHPMailer\PHPMailer\Exception $resException){

					//Handle the exception here

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