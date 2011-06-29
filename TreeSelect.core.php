<?php defined('IN_MANAGER_MODE') or die('<h1>ERROR:</h1><p>Please use the MODx Content Manager instead of accessing this file directly.</p>');

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//
//  Core part of the
//  TreeSelectTV for MODx Evolution
//
//  @version    0.1.4
//  @license    http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
//  @author     sam (sam@gmx-topmail.de)
//
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

global $content,$default_template,$tmplvars;

// Include other parts of the package:
// Class
include $pluginPath."TreeSelect.class.php";

// Config files
$settings = $options = array();

// load default configuration from file
$default_config = $pluginPath."configs/default.config.inc.php";
include $default_config;

// … and from the backend plugin configuration
$settings['input']['tvids']     = $tvids; 
$settings['input']['tplids']    = $tplids;
$settings['input']['roles']     = $roles;
$settings['input']['status']    = $input_status == "hide" ? "" : $input_status;

$settings['list']['separator']          = $list_separator;
$settings['list']['depth']              = $list_depth == -1 ? false : $list_depth;
$settings['list']['hideOnSelect']       = $list_hideOnSelect == "yes" ? true : false;
$settings['list']['sortBy']             = $list_sortBy == "unsorted" ? false : $list_sortBy;
$settings['list']['sortDir']            = $list_sortDirection == "lower -> upper" ? "asc" : "desc";
$settings['list']['sortFirst']          = $list_sortFirst == "not set" ? false : strtolower($list_sortFirst);
$settings['list']['image_view']         = $list_image_view == "yes" ? true : false;
$settings['list']['size_decimals']      = $list_sizeDecimals > 0 ? $list_sizeDecimals : 0;
$settings['list']['path_base']          = strtolower($list_path_base);

$settings['list']['folders__base']      = $list_folders_base;
$settings['list']['folders__start']     = $list_folders_start;
$settings['list']['folders__filter']    = $list_folders_filter;
$settings['list']['folders__accept']    = $list_folders_accept;
$settings['list']['folders__only']      = $list_folders_only == "yes" ? true : false;

$settings['list']['files__filter']      = $list_files_filter;
$settings['list']['files__accept']      = $list_files_accept;

$settings['list']['files__skip_0b']     = $list_files_skip_0b == "yes" ? true : false;
$settings['list']['files__maxsize']     = $list_files_maxsize == -1 ? false : $list_files_maxsize;
$settings['list']['files__minsize']     = $list_files_minsize == -1 ? false : $list_files_minsize;
$settings['list']['files__only']        = $list_files_only == "yes" ? true : false;

$default_settings = $settings;
//unset($settings);

// load custom configuration files
$configFiles = glob($pluginPath.'configs/*.config.inc.php');
//$i = 0;
$ids_found = '';
if (count($configFiles)) {
    foreach ($configFiles as $i => $configFile) {
        $settings = $default_settings;
        if ($configFile != $default_config) include $configFile;
        if (!isset($settings['input']['tvids']) || !strlen($settings['input']['tvids'])) continue;
        $options[$i]['values'] = $settings;
        $options[$i]['file'] = $configFile;
        $ids_found .= (strlen($ids_found) ? "," : "").$settings['input']['tvids'];
//        ++$i;
        unset($settings);
    }
} elseif (!isset($default_settings['input']['tvids']) || !strlen($default_settings['input']['tvids'])) return;
else {
    $options[$i]['values'] = $default_settings;
    $options[$i]['file'] = $default_config;
}

// Initialize things
$tvIds = $htmlTrees = $inputStatus = $files_only = $image_view = $hideOnSelect = $basePaths = $saveConfigs = "";

$cur_tpl    = isset($_POST['template']) ? $_POST['template'] : (isset($content['template']) ? $content['template'] : $default_template);
$cur_role   = $_SESSION['mgrRole'];

// Set options for each TV
foreach ($options as $i => $option) {
    $input_opt = $option['values']['input'];
    $list_opt = $option['values']['list'];
    $sep = $list_opt['separator'];
    
    // Check if the current template matches and user has the right role
    $tpl    = (strlen($input_opt['tplids'])) ? explode(',', $input_opt['tplids']) : false;
    $role   = (strlen($input_opt['roles'])) ? explode(',', $input_opt['roles']) : false;
    if (($tpl && !in_array($cur_tpl, $tpl)) || ($role && !in_array($cur_role, $role))) continue;
    
    if ($list_opt['folders__base'] == "") $list_opt['folders__base'] = MODX_BASE_PATH;
    $tvName = (isset($option['file']) && $option['file']) ? basename($option['file']) : "default";
    $tvName = strlen($tvName) ? substr($tvName, 0, strpos($tvName, '.')) : "";

    $tvIds          .=  (strlen($tvIds) ? "," : "")."[".trim($input_opt['tvids'])."]";
    $inputStatus    .=  (strlen($inputStatus) ? "," : "").
                        (strlen($input_opt['status']) && in_array($input_opt['status'], array("show","toggle")) ?
                        "'".trim($input_opt['status'])."'" :"''");
    $files_only     .=  (strlen($files_only) ? "," : "").
                        ($list_opt['files__only'] && !$list_opt['folders__only'] ? "true" : "false");
    $image_view     .=  (strlen($image_view) ? "," : "").
                        (isset($list_opt['image_view']) && $list_opt['image_view'] ? "true" : "false");
    $hideOnSelect   .=  (strlen($hideOnSelect) ? "," : "").
                        (isset($list_opt['hideOnSelect']) && $list_opt['hideOnSelect'] ? "true" : "false");
    $basePaths      .=  (strlen($basePaths) ? "," : "")."'".
                        (isset($list_opt['path_base']) && ($list_opt['path_base'] != "start folder") ?
                            ($list_opt['path_base'] == "server root" ?
                                $sep.trim(trim($list_opt['folders__base'],$sep).$sep.trim($list_opt['folders__start'],$sep)).$sep :
                                trim($list_opt['folders__start'],"/")."/") : 
                            ""
                        )."'";
    $saveConfigs    .=  (strlen($saveConfigs) ? "," : "")."[".
                        ($cur_role == 1 ? ($tvName == "default" ? "'{$tvName}','save'" : "'{$tvName}','reset','delete'" ) : "''")."]";

    //TODO Implement other methods for generating different HTML coded lists (e.g. image preview or Wayfinder menu)

    // Generate directory listing
    $TreeSelect = new TreeSelect($list_opt);

    if ( is_array($TreeSelect->treeList) && count($TreeSelect->treeList) ) {
        
        // ... and put it into HTML code
        $html_tree = $TreeSelect->list2HTML();
        $htmlTrees .= strlen($html_tree) ? (strlen($htmlTrees) ? "," : "")."'".$html_tree."'" : "";
    }
    unset($TreeSelect);
}
if (!strlen($htmlTrees)) return;

$e = &$modx->Event;
if ($e->name == 'OnDocFormRender') {

    $modx_script = renderFormElement('text',0,'','','');
    preg_match('/(<script[^>]*?>.*?<\/script>)/si', $modx_script, $matches);
    $output = $matches[0];
    $rel_pluginPath = "../".str_replace(MODX_BASE_PATH, '', $pluginPath);
    // Include our JS and CSS file and modify output
    $output .= <<< OUTPUT

<!-- TreeSelect -->
<link rel="stylesheet" type="text/css" href="{$rel_pluginPath}TreeSelect.styles.css" />
<script type="text/javascript" src="{$rel_pluginPath}TreeSelect.class.js"></script>
<script type="text/javascript">
window.addEvent('domready', function() {
    var tvIds           = new Array({$tvIds});
    var trees           = new Array({$htmlTrees});
    var inputStatus     = new Array({$inputStatus});
    var filesOnly       = new Array({$files_only});
    var imageView       = new Array({$image_view});
    var hideOnSelect    = new Array({$hideOnSelect});
    var basePath        = new Array({$basePaths});
    var saveConfigs     = new Array({$saveConfigs});

    for (var i=0; i<tvIds.length; i++) {
        for (var j=0; j<tvIds[i].length; ++j) {
            var inputID = 'tv'+ tvIds[i][j];
            if ($(inputID) != null) { 
                new TreeSelect(inputID,trees[i],inputStatus[i],filesOnly[i],imageView[i],hideOnSelect[i],basePath[i],saveConfigs[i]);
            }
        }
    }   
});
</script>
<!-- /TreeSelect -->

OUTPUT;

    // ... and render it
    $e->output($output);
    
}
if ($e->name == 'OnBeforeDocFormSave') {
    $tvIds = explode(",", str_replace(array("[","]"), "", $tvIds));
    foreach ($tvIds as $tvId) {
        list($path, $save_opt) = json_decode($tmplvars[$tvId][1]);
        if (strlen($save_opt)) {
            list($prefix, $opt) = explode(":", $save_opt);
            $file_path = $pluginPath."configs/".$prefix.".config.inc.php";

            if (file_exists($file_path)) unlink($file_path);
            if (in_array($opt, array("save","reset"))) {
                $settings = $default_settings;

                $settings['input']['tvids'] = (string) $tvId;
                unset($settings['list']['outerTpl']);
                unset($settings['list']['innerTpl']);

                $output =   "<?php defined('IN_MANAGER_MODE') or ".
                            "die('<h1>ERROR:</h1><p>Please use the MODx Content Manager ".
                            "instead of accessing this file directly.</p>');\n\n";
                foreach ($settings['input'] as $key => $value) {
                    $output .=  "\$settings['input']['{$key}'] = ".
                                (is_int($value) ? $value : (is_bool($value) ? ($value ? "true" : "false") : "\"{$value}\"")).
                                ";\n";
                }
                $output .= "\n";
                foreach ($settings['list'] as $key => $value) {
                    $output .=  "\$settings['list']['{$key}'] = ".
                                (is_int($value) ? $value : (is_bool($value) ? ($value ? "true" : "false") : "\"{$value}\"")).
                                ";\n";
                }
                $output .= "\n?>";
                if (file_put_contents($file_path, $output) === false) print_r("ERROR: Could not write \"{$path}\"");
            }
        }
        $tmplvars[$tvId][1] = $path;
    }

}
?>
