<?php
/**
 * Created by PhpStorm.
 * User: laxman
 * Date: 4/2/19
 * Time: 4:09 PM
 */

class Laksh_Automatic_Updates_Plugin{
    const OPTION_KEY = 'laksh_automatic_updates_ignore_list';

    public function getIgnoredPlugins()
    {
        $ignoredPlugins = get_option(self::OPTION_KEY, []);
        if(!is_array($ignoredPlugins)) return [];
        return array_unique($ignoredPlugins);
    }
    protected function updateIgnoredPlugins($ignoredPlugins = [])
    {
        if(!is_array($ignoredPlugins)) return;
        update_option(self::OPTION_KEY, $ignoredPlugins, false);
    }

    public function enablePluginUpdate($pluginSlug)
    {
        $ignoredPlugins = $this->getIgnoredPlugins();
        if(!in_array($pluginSlug, $ignoredPlugins)) return;

        $ignoredPlugins = array_filter($ignoredPlugins, function ($item) use ($pluginSlug){
            if($item == $pluginSlug) return false;
            return true;
        });
        $this->updateIgnoredPlugins($ignoredPlugins);
    }
    public function disablePluginUpdate($pluginSlug)
    {
        $ignoredPlugins = $this->getIgnoredPlugins();
        if(in_array($pluginSlug, $ignoredPlugins)) return;
        $ignoredPlugins[] = $pluginSlug;
        $this->updateIgnoredPlugins($ignoredPlugins);
    }

    public function updatePluginStatus($pluginSlug, $pluginStatus = true)
    {
        if($pluginStatus) {
            $this->enablePluginUpdate($pluginSlug);
            return;
        }
        $this->disablePluginUpdate($pluginSlug);
    }
}