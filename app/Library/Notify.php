<?php
namespace App\Library;

use App\Model\BusRuleRef;
use App\Model\Notification;
use App\Model\User;
use FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use Mail;

class Notify {

	public static function sendMail($view, $data, $subject) {
		$sender_email = env('MAIL_FROM_ADDRESS');
        $sender_name = env('MAIL_FROM_NAME');
        $mail = Mail::send($view, $data, function ($message) use ($data, $sender_email, $sender_name, $subject) {
            $message->to($data['email'], $data['name'])->subject($subject);
            $message->from($sender_email, $sender_name);
        });
        return $mail;
	}
}