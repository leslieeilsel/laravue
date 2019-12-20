<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupProjectFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BackupProjectFile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '备份项目文件';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 创建zip文件
        $zip_file = time() . '.zip';

        $zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $path = storage_path('app/public/project'); // 需要备份的文件夹
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = 'projectBackup/' . substr($filePath, strlen($path) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        // 关闭zip，写入文件
        $zip->close();

        // 上传到ftp服务器
        $result = Storage::disk('ftp')->put($zip_file, fopen(base_path() . '/' . $zip_file, 'r+'));

        // 上传成功，删除源文件
        if ($result) {
            unlink(base_path() . '/' . $zip_file);
        }

        // TODO: 将备份结果写入数据库
    }
}
