<?php defined('IN_MANAGER_MODE') or die('<h1>ERROR:</h1><p>Please use the MODx Content Manager instead of accessing this file directly.</p>');

//==============================================================================
//  IMPORTANT NOTICE:
//
//  Expect of the settings for outerTpl and innerTpl all values in this file
//  will be overwritten by the plugin configuration in the manager backend, so
//  it is not necessary to change the values in this file.
//
//  To set up an individual template variable, make a copy of this file, rename
//  it to *.config.inc.php (where * should be replaced by a name of your choice,
//  e.g. "tv23.config.inc.php") and set the correct values in there.
//  Inside the new file you may delete the lines (and comments) you don't need,
//  except of the first and the last line.
//
//================================================================================

//  Default settings

//---------------
//  Input field
//---------------

$settings['input']['tvids']     = "";
//  String with the id (or a comma separated list of ids) of template variables
//  to be used with. For each list a CSS ID with the name treeBox_tv* will be
//  generated where * will be replaced with the ID of the TV, e.g. treeBox_tv5
//  or treeBox_tv12, so each list can be styled individually

$settings['input']['tplids']    = "";
//  ID of template used ("" = all templates)

$settings['input']['roles']     = "";
//  1 = Administrator, 2 = Editor, 3 = Publisher ("" = all roles)

$settings['input']['status']    = "";
//  Status how to display input field. Options: "show", "toggle" or ""

//---------------
//  List
//---------------
$settings['list']['separator']          = "/";
//  List item separator string for the result string, displayed in the input field

$settings['list']['depth']              = false;
//  Integer value for the level depth of subfolders; Set false for all levels

$settings['list']['sortBy']             = "name";
//  Fields to sort by
//  Options:    "name", "size"

$settings['list']['sortDir']            = "asc";
//  Sorting direction
//  Options:    "asc", "desc"

$settings['list']['sortFirst']          = "folders";
//  Sorting direction
//  Options:    "folders", "files", false

$settings['list']['size_decimals']      = 2;
//  Number of decimals to display filesizes

$settings['list']['hideOnSelect']       = false;
//  Boolean value (true|false); Hides list on clicking item
//  (only if input status is set to "toggle")

$settings['list']['image_view']         = true;
//  Boolean value (true|false); Preview for image files

$settings['list']['path_base']          = "start folder";
//  Base to display path from
//  Options: "start folder", "website base", "server root"

//---------------
//  Folders
//---------------
$settings['list']['folders']['base']    = "";
//  Absolute base path on the server; If empty or not set, the constant
//  MODX_BASE_PATH will be set.

$settings['list']['folders']['start']   = "";
//  Folder location where to start showing folders and files from in the list

$settings['list']['folders']['filter']  = "^\.+";
//  Regular expression of filter string for folders NOT to be listed

$settings['list']['folders']['accept']  = ".*";
// Regular expression of filter string for folders to ACCEPT ONLY

$settings['list']['folders']['only']    = false;
// Boolean value (true|false); Set true to display folders only

//---------------
// Files
//---------------
// Regular expression of filter strings…

$settings['list']['files']['filter']    = "^\.+";
// …for files NOT to be listed

$settings['list']['files']['accept']    = ".*";
// …for files to be listed ONLY e.g. "\.(jpg|png|gif)$";


$settings['list']['files']['skip_0b']   = false;
// Set true to skip files if its size is 0 byte

$settings['list']['files']['maxsize']   = false;
// Maximum size of files to be listed 

$settings['list']['files']['minsize']   = false;
// Minimum size of files to be listed

$settings['list']['files']['only']      = false;
// If this is set true, only folders that contain files are shown in the tree
// and (if "folders only" option is not set) only files are set as result

// HTML semplates with placeholders for the lists
// Outer template:
$settings['list']['outerTpl']   =
    '<ul class="item_group level_[+tsp.level+]">'.
        '[+tsp.wrapper+]'.
    '</ul>';

// Inner Template:
$settings['list']['innerTpl']   =
    '<li class="item_line [+tsp.type+] [+tsp.filetype+] [+tsp.lastItem+]" '.
        'path="[+tsp.path+]" img="[+tsp.img_src+]">'.
        '<span class="folder_status toggler"></span>'.
        '<span class="item_icon toggler"></span>'.
        '<span class="item_text selector">'.
            '<span class="filename">[+tsp.name+]</span>'.
            '<span class="filesize">[+tsp.size+]</span>'.
        '</span>'.
        '[+tsp.wrapper+]'.
    '</li>';

?>
