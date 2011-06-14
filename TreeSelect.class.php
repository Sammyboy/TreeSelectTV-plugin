<?php defined('IN_MANAGER_MODE') or die('<h1>ERROR:</h1><p>Please use the MODx Content Manager instead of accessing this file directly.</p>');

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//
//  Class for the
//  TreeSelectTV for MODx Evolution
//
//  @version    0.1.1
//  @license    http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
//  @author     sam (sam@gmx-topmail.de)
//
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::


class TreeSelect {
    
    function TreeSelect($config) {
        // Initialize things
        $this->config = $config;
        $this->treeList = array();
        $this->treeList = $this->getDirList();
    }
    
    function getDirList(&$list = null, $folder = "", $depth = null) {
    // --> Generates an array of folders and files
        if (!strlen($this->config['folders']['base']) && !strlen($folder)) return;
        if (!isset($list)) $list = $this->treeList;
        $folder         = (strlen($folder) ? trim($folder, "/*") : trim($this->config['folders']['base'], "/*"))."/";
        $depth          = ($depth === null) ? $this->config['depth'] : $depth;
        $folders_only   = $this->config['folders']['only'];
        $files_only     = isset($this->config['files']['only']) ? $this->config['files']['only'] : false;
        if ($handle = opendir(MODX_BASE_PATH.$folder)) {
            while ($file = readdir($handle)) {
                $path = MODX_BASE_PATH.$folder.$file;
                $is_dir = is_dir($path);
                $is_file = is_file($path);
                $has_size = true;
                // Check filesize
                if ($is_file) {
                    $size = filesize($path);
                    if ( ($this->config['files']['skip_0b'] && ($size == 0)) ||
                         ($this->config['files']['minsize'] && ($size < $this->config['files']['minsize'])) ||
                         ($this->config['files']['maxsize'] && ($size > $this->config['files']['maxsize'])) )
                        $has_size = false;
                }
                // Set filters for files and folders
                $filter = $is_dir ? ((isset($this->config['folders']['filter']) && $this->config['folders']['filter'])  ?
                                        $this->config['folders']['filter'] : "") : 
                                    ((isset($this->config['files']['filter'])   && $this->config['files']['filter'])  ?
                                        $this->config['files']['filter'] : "");
                $accept = $is_dir ? ((isset($this->config['folders']['accept']) && $this->config['folders']['accept'])  ?
                                        $this->config['folders']['accept'] : "") : 
                                    ((isset($this->config['files']['accept'])   && $this->config['files']['accept'])    ?
                                        $this->config['files']['accept'] : "");
                // ... and use them
                if ( $has_size &&
                     (($folders_only === false) || ($folders_only && $is_dir)) &&
                     !in_array($file, array(".","..")) && 
                     (!strlen($filter) || !preg_match("/".$filter."/", $file)) &&
                     (!strlen($accept) || preg_match("/".$accept."/", $file)) ) {
                	$list[]['name'] = $file;
                	$key = count($list)-1;
                	if ($is_dir) {
                    	$list[$key]['type'] = 'folder';
                    	// Get subfolders
                    	if (($depth === false) || ($depth > 0)) {
                    	    $list[$key]['subfolder'] = $this->getDirList($list[$key]['subfolder'], $folder.$file, ($depth) ? $depth-1 : $depth);
                    	    if (!is_array($list[$key]['subfolder']) || !count($list[$key]['subfolder'])) unset($list[$key]['subfolder']);
                    	}
                    }  else {
                        $list[$key]['type'] = 'file';
                        // Check if file an image
                        $is_image = getimagesize($path);
                        if ($is_image !== false) {
                            // ... and add it to the array
                            $list[$key]['img']['src'] = '../'.$folder.$file;
                            list($list[$key]['img']['width'], $list[$key]['img']['height']) = $is_image;
                        }
                        if ($size < 1024) $list[$key]['size'] = $size.' B';
                        elseif ($size < 1048576) $list[$key]['size'] = round($size / 1024, 2).' kB';
                        elseif ($size < 1073741824) $list[$key]['size'] = round($size / 1048576, 2).' MB';
                        elseif ($size < 1099511627776) $list[$key]['size'] = round($size / 1073741824, 2).' GB';
                    }
                }
            }
            closedir($handle);
            if ($files_only) $list = $this->removeEmptyFolders($list);
        } else return "Folder {$folder} not found";
        return $list;
    }
    
    function removeEmptyFolders(&$list = null) {
    // --> Removes folders with no files
        if (!isset($list)) $list = $this->treeList;
        foreach ($list as $key => $li) {
            if ($li['type'] == "folder") {
                if (!isset($li['subfolder']) || !count($li['subfolder'])) unset($list[$key]);
            }
        }
        return $list;
    }

    function list2HTML(&$list = null, $path = "", $level = 1, $counter = 0) {
    // --> Generates HTML list output
        if (!isset($list)) $list = $this->treeList;
        // Set configuration parameters
        $separator      = isset($this->config['separator'])         ? $this->config['separator'] : "/";
        $tpl_Outer      = isset($this->config['tpl_Outer'])         ? $this->config['tpl_Outer'] :
                            '<ul class="item_group level_[+tsp.level+]">[+tsp.wrapper+]</ul>';
        $tpl_Inner      = isset($this->config['tpl_Inner'])         ? $this->config['tpl_Inner'] :
                            '<li class="item_line [+tsp.lastItem+]" path="[+tsp.path+]">'.
                            '<span class="item_text">[+tsp.name+]</span>[+tsp.wrapper+]</li>';
        $output = "";
        $last = true;

        foreach ($list as $li) {
            $new_path = $path.$li['name'].($li['type'] == "folder" ? $separator : "");
            $filetype = "";
            if ($li['type'] == 'file') {
                preg_match("/\.(.+)$/", $li['name'], $ext);
                if (count($ext) && strlen($ext[0])) $filetype = "filetype-".strtolower(trim($ext[0], "\."));
            }
            // Set placeholders for row output
            $ph =  array();
            $ph['[+tsp.name+]']     = $li['name'];
            $ph['[+tsp.img_src+]']  = isset($li['img']['src']) ? $li['img']['src'] : "";
            $ph['[+tsp.img_w+]']    = isset($li['img']['width']) ? $li['img']['width'] : "";
            $ph['[+tsp.img_h+]']    = isset($li['img']['height']) ? $li['img']['height'] : "";
            $ph['[+tsp.size+]']     = $li['size'];
            $ph['[+tsp.path+]']     = $new_path;
            $ph['[+tsp.level+]']    = $level;
            $ph['[+tsp.type+]']     = $li['type'];
            $ph['[+tsp.filetype+]'] = $filetype;
            $ph['[+tsp.lastItem+]'] = !isset($li['subfolder']) ? "last_item" : "";
            $ph['[+tsp.wrapper+]']  = isset($li['subfolder']) ? $this->list2HTML($li['subfolder'], $new_path, $level + 1, $counter) : "";
            // ... and parse them
            $output .= str_replace(array_keys($ph), array_values($ph), $tpl_Inner);
        }
        if (strlen($output)) {
            // Set placeholders für list output
            $ph =  array();
            $ph['[+tsp.level+]']    = $level;
            $ph['[+tsp.wrapper+]']  = $output;
            // ... and parse them
            $output = str_replace(array_keys($ph), array_values($ph), $tpl_Outer);
        }

        return $output;
    }

}
?>
