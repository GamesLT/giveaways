<?php

include_once "../../../include/cp_header.php";

define("GIVEAWAYS_DIRNAME", $modversion["dirname"] = basename(dirname(__DIR__)));
define("GIVEAWAYS_URL", ICMS_MODULES_URL . '/' . GIVEAWAYS_DIRNAME . '/');
define("GIVEAWAYS_ROOT_PATH", ICMS_MODULES_PATH . '/' . GIVEAWAYS_DIRNAME . '/');
define("GIVEAWAYS_IMAGES_URL", GIVEAWAYS_URL . 'images/');
define("GIVEAWAYS_ADMIN_URL", GIVEAWAYS_URL . 'admin/');
define('GIVEAWAYS_CORE_PATH', GIVEAWAYS_ROOT_PATH . 'class/core');

// Include the common language file of the module
icms_loadLanguageFile("giveaways", "common");
icms_loadLanguageFile("giveaways", "modinfo");

// Find if the user is admin of the module and make this info available throughout the module
define('GIVEAWAYS_ISADMIN', icms_userIsAdmin(GIVEAWAYS_DIRNAME));

// creating the icmsPersistableRegistry to make it available throughout the module
$icmsPersistableRegistry = icms_ipf_registry_Handler::getInstance();