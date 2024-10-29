<?php
/**
 * Created by PhpStorm.
 * User: laxman
 * Date: 4/2/19
 * Time: 4:09 PM
 */

class Laksh_Automatic_Updates_Core{
    const OPTION_KEY = 'laksh_automatic_updates_core_type';
    const OPTION_NOTIFICATION_KEY = 'laksh_automatic_updates_notification';
    protected $updateTypes = [];

    public function __construct()
    {
        $this->updateTypes = [
            'true' => "Enable all core updates, including minor and major ",
            'minor' => "Enable core updates for minor releases (default)",
            "false" => "Disable all core updates",
        ];
    }

    public function getCoreUpdateTypes()
    {
        return $this->updateTypes;
    }

    public function getCoreUpdateOption()
    {
        //return 1 for default
        $updateType = get_option(self::OPTION_KEY, "minor");
        //return 1 if value from db is other than 0,1 or minor
        if(!array_key_exists($updateType,$this->updateTypes)) return "minor";
        return $updateType;

    }
    public function updateCoreOption($updateType)
    {
        if(!array_key_exists($updateType,$this->updateTypes)) return;
        update_option(self::OPTION_KEY, $updateType, false);
    }

    public function updateNotifications($allowNotification = false)
    {
        update_option(self::OPTION_NOTIFICATION_KEY, $allowNotification);
    }

    public function isNotificationEnabled()
    {
        //return 1 for default
        $isNotificationEnabled = get_option(self::OPTION_NOTIFICATION_KEY);
        //return 1 if value from db is other than 0,1 or minor
        return $isNotificationEnabled;
    }



}