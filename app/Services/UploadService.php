<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use ZipArchive;

class UploadService {

	/**
	 * fn respond file
	 *
	 * @param
	 *        	$filePath
	 * @return mixed
	 */
	public function respondFile($filePath) {
		$file = File::get ( $filePath );
		$type = File::mimeType ( $filePath );

		$response = Response::make ( $file, 200 );
		$response->header ( "Content-Type", $type );

		return $response;
	}

	/**
	 * fn upload file
	 *
	 * @param null $file
	 * @param null $path
	 * @return bool
	 */
	public function uploadFile($file = null, $path = null) {
		if ($file && $path) {
		    $filename = md5 ( time () . '-' . uniqid ()) . '.' . $file->getClientOriginalExtension ();
		    $pathFile = public_path( $path );
			// make directory path
		    if (!File::exists ( $pathFile)) {
		        File::makeDirectory ( $pathFile, 0777, true, true );
		        chmod($pathFile, 0777);
			}

			$file->move ( $pathFile, $filename );

			return $filename;
		}

		return false;
	}

	/**
	 * fn upload file
	 *
	 * @param null $file
	 * @param null $path
	 * @return bool
	 */
	public function makeForder($path = null) {
	    $pathFile = public_path( $path );
	    // make directory path
	    if (!File::exists ( $pathFile)) {
	        $result = File::makeDirectory ( $pathFile, 0777, true , true);
			chmod($pathFile, 0777);
	        return $result;
	    }

	    return false;
	}

	/**
	 * fn remove file
	 *
	 * @param null $path
	 * @param null $filename
	 * @return bool
	 */
	public function removeFile($path = null, $filename = null) {
		if ($path && $filename) {
		    $pathFile = public_path($path);
		    if (!!File::exists ( $pathFile)) {
		        File::delete( $pathFile. '/' . $filename );

				return true;
			}

			return false;
		}

		return false;
	}

	/**
	 * fn clear files in folder
	 *
	 * @param
	 *        	$path
	 * @return bool
	 */
	public function clearFolder($path = null) {
		if (!empty($path)) {
		    if (!!File::exists ( public_path( $path ) )) {
		        File::cleanDirectory ( public_path( $path ) );

				return true;
			}

			return false;
		}

		return false;
	}

	/**
	 * fn delete folder
	 *
	 * @param
	 *        	$path
	 * @return bool
	 */
	public function deleteFolder($path = null) {
		if ($path) {
		    File::deleteDirectory ( public_path( $path ) );

			return true;
		}

		return false;
	}

	/**
	 *  fn create and save content file csv
	 * @param unknown $path
	 * @param unknown $content
	 * @return string|boolean
	 */
	public function saveFileCSV($path, $header, $content, $filename = null) {
	    if($path & $content) {
	        $pathFile = public_path( $path );
	        // make directory path
	        if (!File::exists ( $pathFile )) {
	            File::makeDirectory ( $pathFile, 0777, true, true );
	            chmod($pathFile, 0777);
	        }

	        if($filename == null) {
	            $filename = md5 ( time () . '-' . uniqid ());
	        }

	        $fp = fopen( $pathFile . $filename . '.csv' , "w+");
			// fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
			// header('Content-Type: text/csv; charset=iso-8859-1');
	        if($header) {
	            $content = $header . PHP_EOL . $content;
	        }
	        fwrite($fp, $content);
	        fclose($fp);

	        return $filename;
	    }

	    return false;
	}

	/**
	 * fn check file
	 * @param unknown $fileName
	 * @return boolean
	 */
	public function checkFile($fileName) {
	    $file = public_path($fileName);
	    if (!!File::exists ( $file)) {
	        return $file;
	    }

	    return false;
	}

	/**
	 * FN zip all file in forder
	 * @param string $pathForder
	 * @param string $pathFileZip
	 * @param string $fileName
	 * @return boolean|string
	 */
	public function zipForderCSV($pathForder = "", $pathFileZip = "", $fileName = "file.zip") {
	    $pathFileZip = $pathFileZip  == "" ? public_path("uploads") : $pathFileZip;
	    $pathForder = $pathForder == "" ? public_path("uploads") : $pathForder;

	    // scan file in forder
	    $files = scandir($pathForder);
        unset($files[0], $files[1]);

	    // new file zip
	    $zip = new \ZipArchive();
	    $fileZip = $pathFileZip . $fileName; //path to save the folder and the file name

	    // check file
	    if ($zip->open($fileZip, ZipArchive::CREATE)===TRUE)
	    {
	        foreach($files as $item)
	        {
	            // add file info forder zip
	            $zip->addFile($pathForder . $item, $item);
	        }
	        $zip->close(); // close file zip

	        return $fileZip;
	    }

	    return false;
	}

	public function removeFileCSV($file) {
	    if ($file != "" && !!File::exists ( $file)) {
	        File::delete( $file );

            return true;
        }

        return false;
	}

	public function formatUtf8File ($file = "") {
	    if ($file != "" && !!File::exists ( $file)) {
	        $content = file_get_contents($file);
	        $fp = fopen( $file, "w+");
	        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
	        fwrite($fp, $content);
	        fclose($fp);

	        return true;
	    }

	    return  false;
	}
}
