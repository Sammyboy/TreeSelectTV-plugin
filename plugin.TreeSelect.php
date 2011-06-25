//<?php

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//
//  TreeSelectTV for MODx Evolution
//
//
//  @category   plugin
//  @version    0.1.2
//  @license    http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
//  @author     sam (sam@gmx-topmail.de)
//
/*  @internal   @plugin configuration:

&pluginPath=Plugin path;string;assets/plugins/TreeSelect/
&tvids=TV IDs;string;
&tplids=Template IDs;string;
&roles=Roles;string;
&input_status=Inputfield status;list;hide,show,toggle;hide
&list_separator=Separator;string;/
&list_depth=Tree depth;int;-1
&list_hideOnSelect=Hide on select;list;yes,no;no
&list_image_view=Image preview;list;yes,no;yes
&list_folders_base=Base folder;string;
&list_folders_start=Start folder;string;
&list_folders_filter=Folders to ignore (reg. expr.);string;^\.+
&list_folders_accept=Folders to accept (reg. expr.);string;.*
&list_folders_only=Folders only;list;yes,no;no
&list_files_filter=Files to ignore;string;^\.+
&list_files_accept=Files to accept;string;.*
&list_files_skip_0b=Skip empty files;list;yes,no;no
&list_files_maxsize=Max. filesize;int;-1
&list_files_minsize=Min. filesize;int;-1
&list_files_only=Files only;list;yes,no;no

*/
//  @internal   @events OnDocFormRender
//
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

$_ts_error = "<strong>TreeSelect plugin error</strong>: ";

if (!strlen($pluginPath)) { print_r($_ts_error."Plugin path is not set!"); return; }
$pluginPath = MODX_BASE_PATH.trim($pluginPath, '/').'/';

include $pluginPath."TreeSelect.core.php";
