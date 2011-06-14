<?php defined('IN_MANAGER_MODE') or die('<h1>ERROR:</h1><p>Please use the MODx Content Manager instead of accessing this file directly.</p>');

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//
//  Core part of the
//  TreeSelectTV for MODx Evolution
//
//  @version    0.1.1
//  @license    http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
//  @author     sam (sam@gmx-topmail.de)
//
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

global $content,$default_template,$tmplvars;

// Include other parts of the package:
// Configuration
include $pluginPath."TreeSelect.config.php";
// Class
include $pluginPath."TreeSelect.class.php";

// Initialize things
$tvIds = "";
$htmlTrees = "";
$inputStatus = "";
$files_only = "";
$image_view = "";
$hideOnSelect = "";

$cur_tpl  = isset($_POST['template']) ? $_POST['template'] : (isset($content['template']) ? $content['template'] : $default_template);
$cur_role   = $_SESSION['mgrRole'];

// Set options for each TV
foreach ($TSPC as $option) {
    $input = $option['input'];
    $list_opt = array_merge($TSPC_global['list'], $option['list']);

    // Check if the current template matches and user has the right role
    $tpl    = (isset($input['tpl_id']) && strlen($input['tpl_id'])) ? explode(',', $input['tpl_id']) : false;
    $role   = (isset($input['role']) && strlen($input['role'])) ? explode(',', $input['role']) : false;
    if (($tpl && !in_array($cur_tpl, $tpl)) || ($role && !in_array($cur_role, $role))) continue;

    // Make list of TV
    $tvIds          .=  (strlen($tvIds) ? "," : "").$input['tv_id'];
    $inputStatus    .=  (strlen($inputStatus) ? "," : "").
                        (strlen($input['status']) && in_array($input['status'], array("show","toggle")) ?
                        "'".$input['status']."'" :"''");
    $files_only     .=  (strlen($files_only) ? "," : "").
                        ((isset($list_opt['files']['only']) && $list_opt['files']['only']) ? "true" : "false");
    $image_view     .=  (strlen($image_view) ? "," : "").
                        ((isset($list_opt['image_view']) && $list_opt['image_view']) ? "true" : "false");
    $hideOnSelect   .=  (strlen($hideOnSelect) ? "," : "").
                        ((isset($list_opt['hideOnSelect']) && $list_opt['hideOnSelect']) ? "true" : "false");

    //TODO Implement other methods for generating HTML code lists (e.g. image preview or Wayfinder menu)

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
<script type="text/javascript" src="{$rel_pluginPath}TreeSelect.functions.js"></script>
<script type="text/javascript">
window.addEvent('domready', function() {
    var tvIds       = [{$tvIds}];
    var trees       = [{$htmlTrees}];
    var inputStatus = [{$inputStatus}];
    var filesOnly   = [{$files_only}];
    var imageView   = [{$image_view}];
    var hideOnSelect = [{$hideOnSelect}];

    for (var i=0; i<tvIds.length; i++) {
        var inputID = 'tv'+ tvIds[i];
        if ($(inputID) != null) { 
            var modxFolderSelect = new FolderSelect(inputID,trees[i],inputStatus[i],filesOnly[i],imageView[i],hideOnSelect[i]);
        }
    }   
});
</script>
<!-- /TreeSelect -->

OUTPUT;

    // ... and render it
    $e->output($output);
}

?>
