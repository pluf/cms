<?php

class SaaS_Util
{
    public static function initConfiguration($app){
        // System configuration
        $sysConfig = new SaaS_Configuration();
        $sysConfig->application = $app;
        $sysConfig->key = 'system';
        $sysConfig->type = SaaS_ConfigurationType::SYSTEM;
        $sysConfig->setData("level", 0);
        $sysConfig->owner_write = false;
        $sysConfig->member_write = false;
        $sysConfig->authorized_write = false;
        $sysConfig->other_write = false;
        $sysConfig->owner_read = true;
        $sysConfig->member_read = true;
        $sysConfig->authorized_read = false;
        $sysConfig->other_read = false;
        $sysConfig->create();
        
        // Theme configuration
        $themeConfig = new SaaS_Configuration();
        $themeConfig->application = $app;
        $themeConfig->key = 'theme';
        $themeConfig->type = SaaS_ConfigurationType::GENERAL;
        $themeConfig->setData("id", "g1");
        $themeConfig->setData("style", "default");
        $themeConfig->owner_write = false;
        $themeConfig->member_write = false;
        $themeConfig->authorized_write = false;
        $themeConfig->other_write = false;
        $themeConfig->owner_read = true;
        $themeConfig->member_read = true;
        $themeConfig->authorized_read = true;
        $themeConfig->other_read = true;
        $themeConfig->create();
    }
}