<?php defined('IN_MANAGER_MODE') or die('<h1>ERROR:</h1><p>Please use the MODx Content Manager instead of accessing this file directly.</p>');

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//
//  Core part of the
//  TreeSelectTV for MODx Evolution
//
//  @version    0.1.0
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

$cur_tpl  = isset($_POST['template']) ? $_POST['template'] : (isset($content['template']) ? $content['template'] : $default_template);
$cur_role   = $_SESSION['mgrRole'];

// Set options for each TV
foreach ($TSPC as $option) {
    $input = $option['input'];

    // Check if the current template matches and user has the right role
    $tpl    = (isset($input['tpl_id']) && strlen($input['tpl_id'])) ? explode(',', $input['tpl_id']) : false;
    $role   = (isset($input['role']) && strlen($input['role'])) ? explode(',', $input['role']) : false;
    if (($tpl && !in_array($cur_tpl, $tpl)) || ($role && !in_array($cur_role, $role))) continue;

    // Make list of TV
    $tvIds          .=  (strlen($tvIds) ? "," : "").$input['tv_id'];
    $inputStatus    .=  (strlen($inputStatus) ? "," : "").
                        (strlen($input['status']) && in_array($input['status'], array("show","edit")) ?
                        "'".$input['status']."'" :"''");
    $files_only     .=  (strlen($files_only) ? "," : "").
                        ((isset($option['list']['files']['only']) && $option['list']['files']['only']) ? "true" : "false");

    // Set templates for output
    if (!isset($option['list']['tpl_Outer'])) $option['list']['tpl_Outer'] = $TSPC_Templates['list']['tpl_Outer'];
    if (!isset($option['list']['tpl_Inner'])) $option['list']['tpl_Inner'] = $TSPC_Templates['list']['tpl_Inner'];

    //TODO Implement other methods for generating HTML code lists (e.g. image preview or Wayfinder menu)

    // Generate directory listing
    $TreeSelect = new TreeSelect($option['list']);
    
    if ( is_array($TreeSelect->treeList) && count($TreeSelect->treeList) ) {

        // ... and put it into HTML code
        $html_tree = $TreeSelect->list2HTML();
        $htmlTrees .= strlen($html_tree) ? (strlen($htmlTrees) ? "," : "")."'".$html_tree."'" : "";
    }// else return "";// else print_r($_ts_error."Could not build list!");
    unset($TreeSelect);

}
if (!strlen($htmlTrees)) return;

// Read CSS-Styles from file
$css_file = $pluginPath.'TreeSelect.styles.css';
$css_styles = file_get_contents($css_file);
if (!$css_styles) print_r($_ts_error."Could not find, open or read \"$css_file\"!");

// Read JS functions file
$js_file = $pluginPath.'TreeSelect.functions.js';
$js_code = file_get_contents($js_file);
if (!$js_code) print_r($_ts_error."Could not find, open or read \"$js_file\"!");

// Parse placeholders used in JS code
$ph = array(
    '[+tvIds+]'         => $tvIds,
    '[+htmlTrees+]'     => $htmlTrees,
    '[+inputStatus+]'   => $inputStatus,
    '[+files_only+]'    => $files_only
);
$js_code = str_replace(array_keys($ph), array_values($ph), $js_code);


$e = &$modx->Event;
if ($e->name == 'OnDocFormRender') {

    $modx_script = renderFormElement('text',0,'','','');
    preg_match('/(<script[^>]*?>.*?<\/script>)/si', $modx_script, $matches);
    $output = $matches[0];

    // Include our JS and CSS code
    $output .= "<!-- TreeSelect -->
        <style type=\"text/css\">$css_styles</style>
        <script type=\"text/javascript\">$js_code</script>
        <!-- /TreeSelect -->";

    // ... and render it
    $e->output($output);
}

?>
