<?php
class S_File
{
    /**
     * Take folder content in a list
     * @param $folder folder name
     * @return string list of the files in the folder
     */
    public function folder_files ($folder)
    {	
        $file_list = array();
        if (! is_dir($folder))
            return $file_list;
        if (! ($handle = opendir($folder)))
            return $file_list;
        while (false !== ($file = readdir($handle))) {
            if ($file == "." || $file == "..")
            
                continue;
            $file_list[] = $file;
           
        }
        closedir($handle);
        return $file_list;
    }
    /**
     * Recursive folder copy
     * @param string $src_folder source folder to copy from
     * @param string $dst_folder destination folder to copy to
     * @param boolean $recursive if to copy recursuvely all subfolders. default is true.
     * @return boolean true on success
     */
    public function copy_folder_data ($src_folder, $dst_folder, $recursive = true)
    {
        if ($src_folder == $dst_folder)
            return true;
        if (! $this->folder_files($src_folder))
            return true;
        if (! is_dir($src_folder))
            return false;
        if (! mkdir($dst_folder,0777, true))
            return false;
        if (! ($handle = opendir($src_folder)))
            return false;
        $copy_res = true;
        while (false !== ($file = readdir($handle))) {
            if ($file == "." || $file == "..")
                continue;
            if (is_dir($src_folder . "/" . $file)) {
                if ($recursive)
                    $copy_res = copy_folder_data($src_folder . "/" . $file, 
                    $dst_folder . "/" . $file);
            } else
                $copy_res = copy($src_folder . "/" . $file, 
                $dst_folder . "/" . $file);
            if (! $copy_res)
                break;
        }
        closedir($handle);
        return $copy_res;
    }
    /**
     * Recursive folder remove
     * @param string $folder folser to be removed
     * @param boolean $recursive if to remove recursuvely all subfolders. default is true.
     * @return boolean true on success
     */
    public function remove_folder ($folder, $recursive = 1)
    {
        if (! is_dir($folder))
            return false;
        if (! ($handle = opendir($folder)))
            return false;
        $unlink_res = true;
        while (false !== ($file = readdir($handle))) {
            if ($file == "." || $file == "..")
                continue;
            if (is_dir($folder . "/" . $file)) {
                if ($recursive)
                    $unlink_res = remove_folder($folder . "/" . $file);
            } else
                $unlink_res = unlink($folder . "/" . $file);
            if (! $unlink_res)
                break;
        }
        closedir($handle);
        if ($unlink_res)
            $unlink_res = rmdir($folder);
        return $unlink_res;
    }
}