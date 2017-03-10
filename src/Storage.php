<?php

namespace InvokerArt\UEditor;

use Illuminate\Support\Facades\Storage;

class Storage
{
    public function __construct($allowFiles, $listSize, $path, $request)
    {
        $this->allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);
        $this->listSize = $listSize;
        $this->path = $path;
        $this->request = $request;
    }

    public function getList()
    {

        $size = $this->request->get('size', $this->listSize);
        $start = $this->request->get('start', 0);
        $end = $start + $size;
        /* 获取文件列表 */
        $path = ltrim($this->path, '/');

        $files = $this->getfiles(Storage::allFiles($path), $this->allowFiles);
        if (!count($files)) {
            return [
                "state" => "no match file",
                "list" => array(),
                "start" => $start,
                "total" => count($files)
            ];
        }

        /* 获取指定范围的列表 */
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--) {
            $list[] = $files[$i];
        }


        /* 返回数据 */
        $result = [
            "state" => "SUCCESS",
            "list" => $list,
            "start" => $start,
            "total" => count($files)
        ];

        return $result;
    }
    /**
     * 遍历获取目录下的指定类型的文件
     * @param $path
     * @param array $files
     * @return array
     */
    protected function getfiles($path, $allowFiles, &$files = array())
    {
        foreach ($path as $value) {
            if (preg_match("/\.(".$allowFiles.")$/i", $value)) {
                $files[] = array(
                    'url'=> env('APP_URL').Storage::url($value),
                    'mtime'=> filemtime(storage_path().'/app/public/'.$value)
                );
            }
        }

        return $files;
    }
}
