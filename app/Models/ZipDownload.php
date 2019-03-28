<?php

namespace App\Models;
use Chumper\Zipper\Zipper;

class ZipDownload{
    /** 
     * 压缩文件，后下载
     * params
     * $path 路径 
     * $zhFolderName  中文文件名  
     * $table 表名
    */
    public function downloadImages($path,$data)
    {
        $zipper=new Zipper();
        $path_file='zip_'.time();
        $zipper->make(public_path($path.'/'.$path_file.'.zip'));  //public_path($reduce_path)  压缩之后的文件名
        $this->addFileToZip($path, $zipper,$data);
        return $path.'/'.$path_file.'.zip';
    }
    /** 
     * 向压缩文件里面添加文件
     * params
     * $path 路径 
     * $zip  对象
     * $zhFolderName  中文文件名   
     * $table 表名
    */
    public function addFileToZip($path, $zip,$data) {
        $handler = opendir($path);
        while (($filename = readdir($handler)) !== false) {
            if ($filename != "." && $filename != "..") {
                for($i=0;$i<count($data);$i++){
                    $filename_data=$data[$i]['project_title'];
                    if($filename==$filename_data){
                        $zip->folder($filename_data)->add($path.'/'.$filename_data);
                    }
                }
            }
        }
        // while (($filename = readdir($handler)) !== false) {
        //     if ($filename != "." && $filename != "..") {
        //         // $filename=iconv('utf-8','gbk',$filename);
        //         for($i=0;$i<count($data);$i++){
        //             $filename_data=$data[$i]['month'].'_'.$data[$i]['project_num'];
        //             if($filename==$filename_data){
        //                 $path_file=$path . "/" . $filename;
        //                 if (is_dir($path_file)) {
        //                     $this->addFileToZip($path_file,$zip,$data);
        //                 } else { //将文件加入zip对象
        //                     $zip->addFile($path . "/" . $filename);
        //                 }
        //             }
        //         }
        //     }
        // }
        @closedir($path);
    }
    
}