<?php

namespace App\Http\Traits;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

trait HasFile
{
    public function uploadRequestFile($model, $request, $file_input_name, $request_file = null)
    {
        $main_file = ($file_input_name === null) ? $request_file : $request->file($file_input_name);

        if(config('filesystems.default') == 's3') {
            $path =  Storage::putFile($model, $main_file);
            if($path === false){
                throw new \Exception('S3 error');
            }
            return $path;
            // dd($path);
        } else {
            $file_name = Str::random(20).md5(microtime()).'.'.$main_file->getClientOriginalExtension();
            Storage::disk('public')->putFileAs($model, $main_file, $file_name);
            return "{$model}/".$file_name;
        }
    }

    //Full image path
    public function getFileUrl($field)
    {
        if($field == '') {
            return false;
        }
        if(config('filesystems.default') == 's3') {
            return Storage::disk('s3')->url($field);
            // return Storage::disk('s3')->temporaryUrl($field, Carbon::now()->addWeek());
        } else {
            return url(Storage::disk('public')->url($field));
        }
    }
}

