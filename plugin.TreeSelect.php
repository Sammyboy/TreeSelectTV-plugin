//<?php

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//
//  TreeSelectTV for MODx Evolution
//
//
//  @category   plugin
//  @version    0.1.1
//  @license    http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
//  @author     sam (sam@gmx-topmail.de)
//
//  @internal   @properties &pluginPath=Plugin path:;text;assets/plugins/TreeSelect/
//  @internal   @events OnDocFormRender
//
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

$_ts_error = "<strong>TreeSelect plugin error</strong>: ";

if (!strlen($pluginPath)) { print_r($_ts_error."Plugin path is not set!"); return; }
$pluginPath = MODX_BASE_PATH.trim($pluginPath, "/*")."/";

include $pluginPath."TreeSelect.core.php";
