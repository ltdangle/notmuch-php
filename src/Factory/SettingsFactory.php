<?php

declare(strict_types=1);

namespace Dangle\Mailer\Factory;

use Dangle\Mailer\Model\EmailAccount;
use Dangle\Mailer\Settings;
use Symfony\Component\Yaml\Yaml;

class SettingsFactory
{
    public function __invoke(): Settings
    {
        $arr = Yaml::parseFile('config.yaml');
        $settings = new Settings();
        foreach ($arr as $config) {
            $acc = new EmailAccount();

            // this is a virtual account
            if (array_key_exists('virtual', $config) && true === $config['virtual']) {
                $acc->accountType = 'virtual';
                $acc->inboxShellCommand = $config['inboxShellCommand'];
                $acc->shortName = $config['shortName'];
            } // this is a regular email account
            else {
                $acc->accountType = 'regular';
                $acc->inboxShellCommand = $config['inboxShellCommand'];
                $acc->email = $config['email'];
                $acc->shortName = $config['shortName'];
                $acc->trashFolder = $config['deletedFolder'];
                $acc->dsn = $config['dsn'];
            }

            $settings->addAccount($acc);
        }

        return $settings;
    }
}
