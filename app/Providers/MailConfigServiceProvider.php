<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class MailConfigServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        if (application_installed(true)) {
            if ($fromAddress = email_setting("from_address", env('MAIL_FROM_ADDRESS', 'noreply@yourdomain.com'))) {
                Config::set("mail.from.address", $fromAddress);
            }

            if ($fromName = email_setting("from_name", env('MAIL_FROM_NAME', 'TokenLite'))) {
                Config::set("mail.from.name", $fromName);
            }

            $this->setMailDriverSettings();

            Config::set("mail.markdown.theme", 'nio-mail');
        }
    }

    private function setMailDriverSettings()
    {
        $driver = email_setting("driver", env('MAIL_DRIVER', 'sendmail'));

        switch ($driver) {
            case 'sendmail':
                Config::set("mail.default", "sendmail");
                break;

            case 'smtp':
                $config = array(
                    'transport'     => "smtp",
                    'host'       => email_setting("host", env('MAIL_HOST', 'smtp.mailgun.org')),
                    'port'       => email_setting("port", env('MAIL_PORT', 587)),
                    'encryption' => email_setting("encryption", env('MAIL_ENCRYPTION', 'tls')),
                    'username'   => email_setting("user_name", env('MAIL_USERNAME')),
                    'password'   => email_setting("password", env('MAIL_PASSWORD')),
                    'timeout' => null,
                    'auth_mode' => null,
                );

                Config::set("mail.default", "smtp");
                Config::set("mail.mailers.smtp", $config);
                break;

            case 'mailgun':
                $config = array(
                    'domain' => email_setting('mailgun_domain'),
                    'secret' => email_setting('mailgun_api_key'),
                    'endpoint' => email_setting('mailgun_api_base_url'),
                );
                Config::set("mail.default", "mailgun");
                Config::set("services.mailgun", $config);
                break;

            case 'postmark':
                Config::set("mail.default", "postmark");
                Config::set("services.postmark.token", email_setting('postmark_api_token'));
                break;

            case 'ses':
                $config = array(
                    'key' => email_setting('aws_access_key_id'),
                    'secret' => email_setting('aws_secret_access_key'),
                    'region' => email_setting('aws_default_region'),
                );
                Config::set("mail.default", "ses");
                Config::set("services.ses", $config);
                break;

            case 'sendgrid':
                Config::set("mail.default", "sendgrid");
                Config::set("services.sendgrid.api_key", email_setting('sendgrid_api_key'));
                break;

            default:
                Config::set("mail.default", "sendmail");
                break;
        }
    }
}
