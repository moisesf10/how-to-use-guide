<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

trait FileTrait
{
    /**
     * Converte um base64 de imagem para um arquivo
     *
     * @param $base64_image
     * @param $dirPath 'Absolute Folder path to save image
     * @param null $fileName
     * @return \stdClass {success: boolean, message: string, fileName: string}
     */
    private function convertBase64ToImage($base64_image, $dirPath, $fileName = null, $returnStdClassImg = false){

        $extensions = collect([
            'gif'   =>  'image/gif',
            'png'   =>  'image/png',
            'jpg'   =>  'image/jpg',
            'bmp'   =>  'image/bmp',
            'webp'   =>  'image/webp',
        ]);

        $response = new \stdClass();
        $response->success = false;
        $response->message = null;

        if (preg_match('/^data:image\/(\w+);base64,/', $base64_image)) {

            $data = substr($base64_image, strpos($base64_image, ',') + 1);
            $imageType = preg_replace('/(data:)|(;base64)/i', '', substr($base64_image, 0, strpos($base64_image, ',')));

            $data = base64_decode($data);

            if (empty($imageType)){
                $imageType = 'image/jpg';
            }

            $extension = ($extensions->search($imageType) === false) ? 'jpg' : $extensions->search($imageType);


            $name = null;
            if(empty($fileName)){
                $name = '_' . uniqid(date('HisYmd')) . '.' . $extension;
            }else{
                $name = $fileName . '.' . $extension;
            }

            try{
                $dirPath = preg_replace('/[\/]$/', '', $dirPath);
                // Se não existir o diretório então o cria
                if(! File::isDirectory($dirPath)){
                    File::makeDirectory($dirPath, 0755, true);
                }

                if(! file_put_contents($dirPath . '/' . $name, $data)){
                    if($returnStdClassImg){
                        $response = false;
                    }else{
                        $response->message = 'Falha ao copiar imagem';
                    }

                }else{
                    if($returnStdClassImg){
                        $response->filenameWithExt = $name ;
                        $response->filename = $fileName;
                        $response->extension = $extension;
                        $response->mime = $imageType;
                        $response->size = File::size($dirPath . '/' . $name);
                        $response->filenameToStore = $name;
                        unset($response->success);
                        unset($response->message);

                    }else{
                        $response->success = true;
                        $response->fileName = $name;
                    }

                }

            }catch (\Exception $e){
                Log::error($e->getMessage());
                $response->message = $e->getMessage();
            }

        }



        return $response;
    }

    /**
     * Move um arquivo carregado por upload para o diretório TMP
     * @param $file
     * @param $storageDirPath /Dir path into storage dir
     * @return \stdClass | false
     */
    public function uploadFile($file, $storageDirPath){


        $filenameWithExt = $file->getClientOriginalName();

        // Remove a extensão
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
        $filename = mb_strtolower(preg_replace('/[ \.]/', '_', $filename));

        // Obtém apenas a extensão
        $extension = $file->getClientOriginalExtension();
        // Obtém o mime
        $mime = $file->getClientMimeType();
        // size
        $size = $file->getSize();
        // Filename to store
        $fileNameToStore = date('YmdHis') . '-' .  auth()->user()->id . '-' . $filename. '.'.$extension;

        if(! file_exists(storage_path( $storageDirPath))){
            @mkdir(storage_path( $storageDirPath), 0775, true);
        }


        try{
            // move to TMP PATH

            $file->move(storage_path($storageDirPath) , $fileNameToStore);
        }catch (\Exception $e){
            Log::error('Falha ao importar arquivo para o diretório temporário  em: '.
                storage_path( $storageDirPath . '/' . $fileNameToStore . '; exception: '. PHP_EOL . $e->getMessage()));
            return false;
        }

        $return = new \stdClass();
        $return->filenameWithExt = $filenameWithExt;
        $return->filename = $filename;
        $return->extension = $extension;
        $return->mime = $mime;
        $return->size = $size;
        $return->filenameToStore = $fileNameToStore;
        $return->sha1 = sha1_file(storage_path( $storageDirPath . '/' . $fileNameToStore));

        return $return;

    }

}
