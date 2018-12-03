<?php

$adminmenu[] = array(
    'title' => _MI_GIVEAWAYS_TYPE,
    'link' => 'admin/type.php');

$tm_path = basename(dirname(__DIR__));
$module = icms::handler("icms_module")->getByDirname($tm_path);
$headermenu[] = array(
    'title' => _PREFERENCES,
    'link' => '../../system/admin.php?fct=preferences&amp;op=showmod&amp;mod=' . $module->getVar('mid'));

$headermenu[] = array(
    'title' => _CO_ICMS_GOTOMODULE,
    'link' => ICMS_URL . '/modules/giveaways/');

$headermenu[] = array(
    'title' => _CO_ICMS_UPDATE_MODULE,
    'link' => ICMS_URL . '/modules/system/admin.php?fct=modulesadmin&op=update&amp;module=' . $module->getVar('dirname'));

$headermenu[] = array(
    'title' => _MODABOUT_ABOUT,
    'link' => ICMS_URL . '/modules/giveaways/admin/about.php');