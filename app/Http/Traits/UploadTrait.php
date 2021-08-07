<?php


namespace App\Http\Traits;

use App\Models\Upload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait UploadTrait
{
    /**
     * @param UploadedFile $uploadedFile
     * @param null $folder ,
     * @param string $disk ,
     * @param null $filename
     * @return mixed $file
     */
    public function uploadFile(UploadedFile $uploadedFile, $folder = null, $disk = 'public', $filename = null, $type = null)
    {
        $name = !is_null($filename) ? $filename : Str::random(25);

        $file = $uploadedFile->storeAs($folder, $name . '.' . $uploadedFile->getClientOriginalExtension(), $disk);

        $upload = new Upload();
        $upload->path = 'storage/' . $file;
        $upload->type = $type;
        $upload->save();
        return $upload->id;
    }

}
