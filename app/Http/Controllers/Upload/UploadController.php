<?php

namespace App\Http\Controllers\Upload;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    /**
     * 上传文件，并返回文件的一些详细信息
     *
     * @param  Request  $request
     *
     * @return mixed
     */
    public function uploadFile(Request $request)
    {
        $uploadedFile = $request->file('file');

        $saveDir = $request->has('saveDir') ? $request->input('saveDir') : 'upload';
        $fileType = $request->has('type') ? $request->input('type') : 'file';

        // 文件大小（Kb）
        $fileSize = $uploadedFile->getSize();
        // 扩展名
        $extension = $uploadedFile->getClientOriginalExtension();
        // 文件名
        $fileName = $uploadedFile->getClientOriginalName();

        // 获取用户
        $user = auth()->user();
        $userName = $user->name;
        $userId = $user->id;

        Storage::putFileAs(
            'public/'.$saveDir.'/'.$fileType,
            $uploadedFile,
            $fileName
        );

        $path = config('app.url').'/storage/'.$saveDir.'/'.$fileType.'/'.$fileName;
        return response()->formatJson([
            'success' => true,
            'url'     => $path,
            'detail'  => [
                'file_name'  => $fileName,
                'file_path'  => $path,
                'file_size'  => $fileSize,
                'file_type'  => $extension,
                'created_at' => date('Y-m-d H:i:s'),
                'user_name'  => $userName,
                'user_id'    => $userId,
                'note'       => '',
            ],
        ], '上传成功');
    }
}
