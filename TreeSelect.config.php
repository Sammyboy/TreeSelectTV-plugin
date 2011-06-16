<?php defined('IN_MANAGER_MODE') or die('<h1>ERROR:</h1><p>Please use the MODx Content Manager instead of accessing this file directly.</p>');

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//
//  Configuration file for the
//  TreeSelectTV for MODx Evolution
//
//  (TSPC = ̱Tree̱Select ̱Plugin ̱Configuration)
//
//  @version    0.1.1
//  @license    http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
//  @author     sam (sam@gmx-topmail.de)
//
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::


// All list options can be set either in then global or in the custum section.

// GLOBAL SECTION

// Global settings for values that are not defined in the custom section
$TSPC_global['list']['tpl_Outer']   = '<ul class="item_group level_[+tsp.level+]">[+tsp.wrapper+]</ul>';
$TSPC_global['list']['tpl_Inner']   = '<li class="item_line [+tsp.type+] [+tsp.filetype+] [+tsp.lastItem+]" '.
                                      'path="[+tsp.path+]" img="[+tsp.img_src+]">'.
                                      '<span class="folder_status toggler"></span><span class="item_icon toggler"></span>'.
                                      '<span class="item_text selector"><span class="filename">[+tsp.name+]</span>'.
                                      '<span class="filesize">[+tsp.size+]</span></span>'.
                                      '[+tsp.wrapper+]</li>';
$TSPC_global['list']['separator']           = "/";
$TSPC_global['list']['folders']['filter']   = "^\.+";
$TSPC_global['list']['folders']['accept']   = ".*";
$TSPC_global['list']['hideOnSelect']        = true;
$TSPC_global['list']['image_view']          = true;
$TSPC_global['list']['files']['skip_0b']    = false;
$TSPC_global['list']['files']['maxsize']    = false;
$TSPC_global['list']['files']['minsize']    = false;



// CUSTOM SECTION

// Custom configuration
/*
// Example:
//==============================================================================
// treeTV
//==============================================================================
$c = count($TSPC);

$TSPC[$c]['input']['tv_id']            = 29;        // ID of template variable to be used with the CSS ID is then treeBox_tv29
$TSPC[$c]['input']['tpl_id']           = "5";       // ID of template used
$TSPC[$c]['input']['role']             = false;     // 1 = Administrator, 2 = Editor, 3 = Publisher
$TSPC[$c]['input']['status']           = "show";    // Status how to display input field. Options: "show", "toggle" or ""


$TSPC[$c]['list']['separator']         = "/";       // List item separator string for output string (displayed in the input field)
$TSPC[$c]['list']['depth']             = false;     // Depth of level of subfolders; Set false for all levels
$TSPC[$c]['list']['hideOnSelect']      = false;     // hides list on clicking item (only if input status is "toggle")
$TSPC[$c]['list']['image_view']        = true;      // Preview for image files

$TSPC[$c]['list']['folders']['base']   = "assets/images"; // Folder to be listed
$TSPC[$c]['list']['folders']['filter'] = "^\.+";    // Regular expression of filter string for folders NOT to be listed
$TSPC[$c]['list']['folders']['accept'] = ".*";      // Regular expression of filter string for folders to ACCEPT ONLY
$TSPC[$c]['list']['folders']['only']   = true;      // Set true to display folders only

$TSPC[$c]['list']['files']['filter']   = "^\.+";    // Regular expression of filter string for files NOT to be listed
$TSPC[$c]['list']['files']['accept']   = ".*";      // Regular expression of filter string for files to be listed ONLY e.g. "\.(jpg|png|gif)$";
$TSPC[$c]['list']['files']['skip_0b']  = false;     // Set true to skip files if its size is 0 byte
$TSPC[$c]['list']['files']['maxsize']  = false;     // Maximum size of files to be listed 
$TSPC[$c]['list']['files']['minsize']  = false;     // Minimum size of files to be listed
$TSPC[$c]['list']['files']['only']     = false;     // If this is set true, only folders that contain files are shown in the tree
                                                    // and (if "folders only" option is not set) only files are set as result

$TSPC[$c]['list']['tpl_Outer']         = '<ul class="item_group level_[+tsp.level+]">[+tsp.wrapper+]</ul>';    // HTML-Template for list
$TSPC[$c]['list']['tpl_Inner']         = '<li class="item_line [+tsp.type+] [+tsp.filetype+] [+tsp.lastItem+]" path="[+tsp.path+]"></span>'.
                                         '<span class="item_text selector">[+tsp.name+]</span>[+tsp.wrapper+]</li>'; // HTML-Template for list items

*/

//==============================================================================
// treeTV
//==============================================================================
$c = count($TSPC);

$TSPC[$c]['input']['tv_id']             = 29;
$TSPC[$c]['input']['tpl_id']            = "5";
$TSPC[$c]['input']['role']              = false;
$TSPC[$c]['input']['status']            = "";

//$TSPC[$c]['list']['separator']          = "/";
$TSPC[$c]['list']['depth']              = false;

$TSPC[$c]['list']['folders']['base']    = "assets/files";
//$TSPC[$c]['list']['folders']['filter']  = "^\.+";
//$TSPC[$c]['list']['folders']['accept']  = ".*";
$TSPC[$c]['list']['folders']['only']    = true;

$TSPC[$c]['list']['files']['filter']    = "^\.+";
$TSPC[$c]['list']['files']['accept']    = ".*";
//$TSPC[$c]['list']['files']['skip_0b']   = false;
//$TSPC[$c]['list']['files']['maxsize']   = false;
//$TSPC[$c]['list']['files']['minsize']   = false;

//$TSPC[$c]['list']['tpl_Outer']          = null;
//$TSPC[$c]['list']['tpl_Inner']          = null;

//==============================================================================
// anotherTreeTV
//==============================================================================
$c = count($TSPC);

$TSPC[$c]['input']['tv_id']             = 30;
$TSPC[$c]['input']['tpl_id']            = "5";
$TSPC[$c]['input']['role']              = false;
$TSPC[$c]['input']['status']            = "show";

//$TSPC[$c]['list']['separator']          = "/";
$TSPC[$c]['list']['depth']              = false;
$TSPC[$c]['list']['hideOnSelect']       = true;

$TSPC[$c]['list']['folders']['base']    = "assets/images";
//$TSPC[$c]['list']['folders']['filter']  = "^\.+";
//$TSPC[$c]['list']['folders']['accept']  = ".*";
$TSPC[$c]['list']['folders']['only']    = false;

$TSPC[$c]['list']['files']['filter']    = "^\.+";
$TSPC[$c]['list']['files']['accept']    = "\.(png|jpg)$";
//$TSPC[$c]['list']['files']['skip_0b']   = false;
//$TSPC[$c]['list']['files']['maxsize']   = false;
//$TSPC[$c]['list']['files']['minsize']   = false;
$TSPC[$c]['list']['files']['only']      = true;

//$TSPC[$c]['list']['tpl_Outer']          = $TSPC_Templates['list']['tpl_Outer'];
//$TSPC[$c]['list']['tpl_Inner']          = $TSPC_Templates['list']['tpl_Inner'];

?>
