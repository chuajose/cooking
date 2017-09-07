<?php

namespace App\Services;

use App\File;
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
    var $company_id = false; 

    /**
     * Construcctor
     * 
     * Create instancia for s3 storage
     */
    public function __construct()
    {
        $this->s3Storage = \Storage::disk('s3');
    }
    
    /**
     * Validate data from file
     *
     * @param array $data data to validate
     * 
     * @return void
     */
    protected function validator(array $data, $type = null)
    {
        return Validator::make($data, $this->validators);
    }
    /**
     * Get file
     * Get file from database and return data
     * 
     * @param int   $uid    id for database file
     * @param array $fields field to show in return
     * 
     * @return void array with file data
     */
    public function get($uid, array $fields = [])
    {
        $file = File::find($uid);
        $this->s3Client = $this->getS3();
        if (!$this->s3Client) return false;

        /*$command =$this->s3Client->getCommand('GetObject', array(
            'Bucket' => 'pruebaslaravel',
            'Key' => $file->name,
        ));

        $signedUrl = $this->s3Client->createPresignedRequest($command, '+10 minutes');
        $file['aws'] = (string) $signedUrl->getUri();
        */

        $file['real_path'] = $this->s3Storage->url($file->name);
        //$file['real_path'] = $this->s3Client->getObjectUrl('cepyme500', $file->name); 
        $fileData = array();
        if (is_array($fields) && !empty($fields)) {
            foreach ($fields as $field) {
                if (isset($file[$field])) {
                    $fileData[$field] = $file[$field];
                }
            }
        } else {
            $fileData = $file;
        }

        return $fileData;
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
  
        $validator = $this->validator(Request::all());
        if ($validator->fails()) {
            throw new ValidationCepymeException($validator);
        }

        if (gettype($name) === 'object') {

            $file = $name;
        } else {
            $file = Request::file($name);
        }

        if (!$file || !$file->isValid()) {
            return false;
        }
        
        $hash = md5($file->getClientOriginalName(). time()).'.'.$file->getClientOriginalExtension();

        $name = $file->storeAs($path, $hash, ['disk' => 's3', 'visibility' => $acl]);
        $path = rtrim($path, '/') . '/';

        DB::beginTransaction();
        $addFile = new File;
        $addFile->original_name = $file->getClientOriginalName();
        $addFile->name = $hash;
        $addFile->type = $file->getClientOriginalExtension();
        $addFile->path = $path;

        $addFile->company_id = $this->company_id ? $this->company_id : ((!\Auth::guest()) ? \Auth::user()->company->id : null);
        if ($addFile->save()) {
            DB::commit();
            return $addFile;
        } else {
            DB::rollBack();
            // return exception
        }
        return false;
    }

    /**
     * Upload file
     *
     * @param string $url Url of file
     * @param string $path path to upload file
     * @param string $acl  permissions for file
     * 
     * @return boolean
     */
    public function uploadUrl($url, $path = "", $acl = "public")
    {
        if (!$url) {
            return false;
        }
        if (filter_var($url, FILTER_VALIDATE_URL) === false)
        {
            return null;
        }
        $file = file_get_contents($url);

        if (!$file) {
            return null;
        }
        $extension = pathinfo($url, PATHINFO_EXTENSION);
        if (!$extension) {
            foreach ($http_response_header as $key => $value) {
                $data = explode(':', $value);
                if (trim($data[0] == 'Content-Type')) {
                    $content = explode('/', trim($data[1]));
                    $extension = array_pop($content);
                    break;
                }
            }
        }
        $file_name = 'file.'.$extension;
        $path = rtrim($path, '/') . '/';
        $hash = md5($file_name. time()).'.'.$extension;

        $send = $this->s3Storage->put($hash, $file, $acl);
        if ($send) {
            DB::beginTransaction();
            $addFile = new File;
            $addFile->original_name = $file_name;
            $addFile->name = $hash;
            $addFile->type = $extension;
            $addFile->path = $path;
            $addFile->company_id = $this->company_id ? $this->company_id : ((!\Auth::guest()) ? \Auth::user()->company->id : null);
            if ($addFile->save()) {
                
                DB::commit();
                return $addFile;
            } else {
                DB::rollBack();
                // return exception
            }
        }
        return false;
    }

    /**
     * Upload file to S3
     *
     * @param string $file object file to upload
     * @param string $path path to save file
     * @param string $acl  permission to file
     * 
     * @return boolean
     */
    function uploadFileOrUrl($file = "", $type = "file")
    {
        
        $request = new Request;
        
        $fileObj = null;
        if (Request::hasFile($file)) {
            $this->validators[$file] = $type . '|max:'.$this->maxFile;
            $fileObj = $this->upload($file);
        } elseif (Request::input($file)) {
            $fileObj = $this->uploadUrl(Request::input($file));
        }
        if ($fileObj) {
            return $fileObj->id;
        }
        return null;
    }

    /**
     * Upload file to S3
     *
     * @param string $file object file to upload
     * @param string $path path to save file
     * @param string $acl  permission to file
     * 
     * @return boolean
     */
    function uploadS3($file = "", $path="", $acl = "public")
    {
        if (!$this->s3Storage) {
            return false;
        }
        
        $send = $this->s3Storage->put($path, file_get_contents($file), $acl);
        return $send;
    }

    /**
     * Delete file
     *
     * @param string $path path to file delete
     * 
     * @return boolean
     */
    function remove($path)
    {
        if (!$this->s3Storage) {
            return false;
        }

        return  $this->s3Storage->delete($path);
    }

    /**
     * Delete file by id
     *
     * @param int $id id from database record
     * 
     * @return boolean
     */
    function removeById($id)
    {

        $file = File::find($id);
        if (!$file) {
            return false;
        }

        if ($this->remove($file->path.$file->name)) {
            $file->delete();//delete form database
            return true;
        }
    }

    /**
     * Get file url in s3
     *
     * @param  String $name Name of the file
     * @return String
     */
    function getUrl($name) 
    {
        return  $this->s3Storage->url($name);
    }


    function listFilesS3(){
        $this->s3Client = $this->getS3();
        $objects =  $this->s3Client->getIterator('ListObjects', array('Bucket' => 'cepyme500'));
        return $objects;
        echo "Keys retrieved!\n";
        foreach ($objects as $object) {
            echo $object['Key'] . "\n";
        }
    }

    function deleteS3($key)
    {
        $this->s3Client = $this->getS3();
        $result = $this->s3Client->deleteObject(array(
            'Bucket' => 'cepyme500',
            'Key'    => $key
        ));       
        return $result;
    }

}