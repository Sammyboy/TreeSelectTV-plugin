<?php defined('IN_MANAGER_MODE') or die('<h1>ERROR:</h1><p>Please use the MODx Content Manager instead of accessing this file directly.</p>');

//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//
//  Class for the
//  TreeSelectTV for MODx Evolution
//
//  @version    0.1.3
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

    protected function format_bytes($bytes, $round_nr) {
    // --> Formats filesizes to a shorter format
        if ($bytes < 1024) return $bytes.' B';
        elseif ($bytes < 1048576) return number_format(round($bytes / 1024, $round_nr), $round_nr).' KB';
        elseif ($bytes < 1073741824) return number_format(round($bytes / 1048576, $round_nr), $round_nr).' MB';
        elseif ($bytes < 1099511627776) return number_format(round($bytes / 1073741824, $round_nr), $round_nr).' GB';
        else return number_format(round($bytes / 1099511627776, $round_nr), $round_nr).' TB';
    }
    
    function getDirList(&$list = null, $folder = "", $depth = null) {
    // --> Generates an array of folders and files
        if (!strlen($this->config['folders']['start']) && !strlen($folder)) return;
        if (!isset($list)) $list = $this->treeList;
        $folder         = (strlen($folder) ? trim($folder, "/") : trim($this->config['folders']['start'], "/"))."/";
        $depth          = ($depth === null) ? $this->config['depth'] : $depth;
        $folders_only   = $this->config['folders']['only'];
        $files_only     = isset($this->config['files']['only']) ? $this->config['files']['only'] : false;
        if ($handle = opendir($this->config['folders']['base'].$folder)) {
            while ($file = readdir($handle)) {
                $path = $this->config['folders']['base'].$folder.$file;
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
                // … and use them
                if ( $has_size &&
                     (($folders_only === false) || ($folders_only && $is_dir)) &&
                     !in_array($file, array(".","..")) && 
                     (!strlen($filter) || !preg_match("/".$filter."/", $file)) &&
                     (!strlen($accept) || preg_match("/".$accept."/", $file)) ) {

                    $key = count($list);

                    $list[$key]['name'] = $file;
                    $list[$key]['size'] = $size;
                    $list[$key]['formated_size'] = $this->format_bytes($size, $this->config['size_decimals']);

                    if ($is_dir) {
                        $list[$key]['type'] = 'folder';
                        // Get subfolders
                        if (($depth === false) || ($depth > 0)) {
                            $list[$key]['subfolder'] = $this->getDirList($list[$key]['subfolder'], $folder.$file, ($depth) ? $depth-1 : $depth);
                            if (!is_array($list[$key]['subfolder']) || !count($list[$key]['subfolder'])) unset($list[$key]['subfolder']);
                        }
                        
                    }  else {
                        $list[$key]['type'] = 'file';
                        // Check if file is an image
                        $is_image = getimagesize($path);
                        if ($is_image !== false) {
                            // … and add it to the array
                            $list[$key]['img']['src'] = '../'.$folder.$file;
                            list($list[$key]['img']['width'], $list[$key]['img']['height']) = $is_image;
                        }
                    }
                }
            }
            closedir($handle);
            // remove empty folders
            if ($files_only) $list = $this->removeEmptyFolders($list);
            // sort the list
            $list = $this->sortList($list);
        } else return "Folder {$folder} not found";
        return $list;
    }
    
    function sortList(&$list = null) {
    // --> Sorts the list by the options, set in the configuration
        if (!isset($list)) $list = $this->treeList;
        if (!isset($list)) return false;
        if (!$this->config['sortBy'] || !in_array($this->config['sortBy'], array("name","size")) || (count($list) < 2)) return $list;

        // outer loop
        for ($key_a = count($list)-1; $key_a >= 0; $key_a--) {
            $sorted = true;
            // inner loop
            for ($key_b = 0; $key_b < $key_a; $key_b++) {
                // set key values depending on the sorting direction
                if ($this->config['sortDir'] == "desc") {
                    $k[0] = $key_b;
                    $k[1] = $key_b + 1;
                } else {
                    $k[0] = $key_b + 1;
                    $k[1] = $key_b;
                }

                if (($list[$k[0]]['type'] != $list[$k[1]]['type']) && $this->config['sortFirst']) {
                    if ((($this->config['sortFirst'] == "folders") && ($this->config['sortDir'] == "asc")) ||
                         ($this->config['sortFirst'] == "files") && ($this->config['sortDir'] == "desc"))
                        // folders first 
                        $res = ($list[$k[0]]['type'] == 'folder') && ($list[$k[1]]['type'] == 'file') ? -1 : 1;
                    else
                        // files first
                        $res = ($list[$k[0]]['type'] == 'folder') && ($list[$k[1]]['type'] == 'file') ? 1 : -1;
                } else {
                    // set the values to sort by
                    $val[0] = $list[$k[0]][$this->config['sortBy']];
                    $val[1] = $list[$k[1]][$this->config['sortBy']];
                    // compare the values
                    $res = is_numeric($val[0]) && is_numeric($val[1]) ? $val[0] - $val[1] : strcmp($val[0], $val[1]);
                }

                // swap the list items
                if ($res < 0) {
                    $tmp = $list[$k[0]];
                    $list[$k[0]] = $list[$k[1]];
                    $list[$k[1]] = $tmp;
                    $sorted = false;
                }

            } // end inner loop
            if ($sorted) return $list;
        } // end outer loop
    }

    function removeEmptyFolders(&$list = null) {
    // --> Removes folders with no files
        if (!isset($list)) $list = $this->treeList;
        foreach ($list as $key => $li) {
            if ($li['type'] == "folder") {
                if (!isset($li['subfolder']) || !count($li['subfolder'])) unset($list[$key]);
            }
        }
        sort($list);
        reset($list);
        return $list;
    }

    function list2HTML(&$list = null, $path = "", $level = 1, $counter = 0) {
    // --> Generates HTML list output
        if (!isset($list)) $list = $this->treeList;
        // set configuration parameters
        $separator      = isset($this->config['separator'])         ? $this->config['separator'] : "/";
        $outerTpl      = isset($this->config['outerTpl'])         ? $this->config['outerTpl'] :
                            '<ul class="item_group level_[+tsp.level+]">[+tsp.wrapper+]</ul>';
        $innerTpl      = isset($this->config['innerTpl'])         ? $this->config['innerTpl'] :
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
            // set placeholders for row output
            $ph =  array();
            $ph['[+tsp.name+]']     = $li['name'];
            $ph['[+tsp.img_src+]']  = isset($li['img']['src']) ? $li['img']['src'] : "";
            $ph['[+tsp.img_w+]']    = isset($li['img']['width']) ? $li['img']['width'] : "";
            $ph['[+tsp.img_h+]']    = isset($li['img']['height']) ? $li['img']['height'] : "";
            $ph['[+tsp.size+]']     = $li['type'] == 'file' ? $li['formated_size'] : null;
            $ph['[+tsp.path+]']     = $new_path;
            $ph['[+tsp.level+]']    = $level;
            $ph['[+tsp.type+]']     = $li['type'];
            $ph['[+tsp.filetype+]'] = $filetype;
            $ph['[+tsp.lastItem+]'] = !isset($li['subfolder']) ? "last_item" : "";
            $ph['[+tsp.wrapper+]']  = isset($li['subfolder']) ? $this->list2HTML($li['subfolder'], $new_path, $level + 1, $counter) : "";
            // … and parse them
            $output .= str_replace(array_keys($ph), array_values($ph), $innerTpl);
        }
        if (strlen($output)) {
            // set placeholders für list output
            $ph =  array();
            $ph['[+tsp.level+]']    = $level;
            $ph['[+tsp.wrapper+]']  = $output;
            // … and parse them
            $output = str_replace(array_keys($ph), array_values($ph), $outerTpl);
        }

        return $output;
    }

}
?>
