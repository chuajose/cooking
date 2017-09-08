<?php

namespace App\Services;

use App\Models\Files\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Validator;

/**
 * File Service
 * 
 * @category Manager_Files
 * @package  FileService
 * @author   Jose Suarez <chua.jose@gmail.com>
 * @license  MIT http://fsf.org/
 * @link     http://url.com
 */
class FileService extends AwsService
{
    var $minutes = 1;
    var $cache = false;
    var $s3Storage = false;
    var $maxFile = 500000;
    var $validators = [
        /*'file' => 'max:500000|mimes:jpeg,bmp,png,jpeg',
        'images' => 'max:500000|mimes:jpeg,jpg,png,bmp',
        'videos' => 'max:500000|mimes:mp4'*/
    ];
    var $user_id = false; 

    /**
     * Construcctor
     * 
     * Create instancia for s3 storage
     */
    public function __construct()
    {
        //$this->s3Storage = \Storage::disk('s3');

    }
    
    
    /**
     * Upload file
     *
     * @param string $name name of file
     * @param string $path path to upload file
     * @param string $acl  permissions for file
     * 
     * @return boolean
     */
    public function upload($name = "file", $path = "", $acl = "public")
    {
  
        /*$validator = $this->validator(Request::all());
        if ($validator->fails()) {
          //  throw new ValidationCepymeException($validator);
          dd('mal validado');
        }
*/
        if (gettype($name) === 'object') {

            $file = $name;
        } else {
            $file = Request::file($name);
        }

        if (!$file || !$file->isValid()) {
            return false;
        }
        
        $hash = md5($file->getClientOriginalName(). time()).'.'.$file->getClientOriginalExtension();

        $name = $file->storeAs($path, $hash);
        
        
        $path = rtrim($path, '/') . '/';

        DB::beginTransaction();
        $addFile = new File;
        $addFile->original_name = $file->getClientOriginalName();
        $addFile->name = $hash;
        $addFile->type = $file->getClientOriginalExtension();
        $addFile->path = $path;

        $addFile->user_id = $this->user_id ? $this->user_id : ((!\Auth::guest()) ? \Auth::User()->id : null);
        if ($addFile->save()) {
            DB::commit();
            return $addFile;
        } else {
            DB::rollBack();
            // return exception
        }
        return false;
    }

    

}