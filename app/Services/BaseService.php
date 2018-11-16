<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class BaseService {

	/**
	 * fn response success
	 *
	 * @param array $data
	 * @return object
	 */
	public function success($data = array()) {
		return ( object ) array (
				'data' => $data,
				'status' => true
		);
	}

	/**
	 * fn response error
	 *
	 * @param array $error
	 * @return object
	 */
	public function fail($error = array()) {
		return ( object ) array (
				'error' => $error,
				'status' => false
		);
	}

	/**
	 * fn not found
	 *
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function notFound() {
		return redirect ( '/404' );
	}

	/**
	 * fn error server
	 *
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function errorServer() {
		return redirect ( '/500' );
	}

	/**
	 * FN GET TIMEZONE
	 * @param string $timeZone
	 * @throws Exception
	 * @return array | string
	 */
	public static function getTimeZone( $timeZone = null ){
	    $path = storage_path("/json/timezones.json");
	    if (!File::exists($path)) {
	        throw new Exception("Invalid File timezones.json");
	    }

	    $file = (array)json_decode(File::get($path));
	    if ($timeZone){
	        return $file[$timeZone];
	    } else {
    	       return $file;
	    }
	}

	/**
	 * FN Returns the offset from the origin timezone to the remote timezone, in seconds.
	 * @param $remote_tz
	 * @param $origin_tz: If null the servers current timezone is used as the origin.
	 * @return boolean|number
	 */
	public static function getTimezoneOffset($remote_tz, $origin_tz = null) {
	    if($origin_tz === null) {
	        if(!is_string($origin_tz = date_default_timezone_get())) {
	            return false; // A UTC timestamp was returned -- bail out!
	        }
	    }
	    $origin_dtz = new \DateTimeZone($origin_tz);
	    $remote_dtz = new \DateTimeZone($remote_tz);
	    $origin_dt = new \DateTime("now", $origin_dtz);
	    $remote_dt = new \DateTime("now", $remote_dtz);
	    $offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
	    return $offset;
	}

	/**
	 * FN Returns the current time by timezone
	 * @param unknown $timezone
	 * @return number
	 */
	public static function getCurrentTimeByTimeZone( $timezone ) {
	    $timeOffset = self::getTimezoneOffset( $timezone );
	    return time() - $timeOffset;
	}

	/**
	 * FN get country
	 * link data: https://gist.github.com/keeguon/2310008
	 * @param string $codeCountry
	 * @return string|array
	 */
	public function getCountry($codeCountry = null) {
        $pathFile = storage_path("/json/countries.json");

        if(!File::exists($pathFile)) {
            return null;
        }

        $countries = File::get($pathFile);
        $file = (array)json_decode(File::get($pathFile));
        if ($codeCountry != "" && $codeCountry != null){
            return $file[$codeCountry];
        }

        return $file; // return all country is default
	}

	/**
	 * FN get currency
	 * link data json: https://gist.github.com/Fluidbyte/2973986
	 * @param unknown $codeCountry
	 * @return NULL|mixed
	 */
	public function getCurrency($codeCountry = null) {
	    $pathFile = storage_path("/json/currencies.json");

	    if(!File::exists($pathFile)) {
	        return null;
	    }

	    $countries = File::get($pathFile);
	    $file = (array)json_decode(File::get($pathFile));
	    if ($codeCountry){
	        return $file[$codeCountry];
	    }

	    return $file;
	}

	/**
	 * FN get language list
	 * link data json: https://gist.github.com/piraveen/fafd0d984b2236e809d03a0e306c8a4d
	 * @param string $codeLanguage
	 * @return array|string
	 */
	public function getLanguage($codeLanguage = null) {
	    $pathFile = storage_path("/json/languages.json");

	    if(!File::exists($pathFile)) {
	        return null;
	    }

	    $languages = File::get($pathFile);
	    $file = (array)json_decode(File::get($pathFile));
	    if ($codeLanguage){
	        return $file[$codeLanguage];
	    }

	    return $file; // return all languages is default
	}

	/**
	 * FN get default language
	 * link data json: https://gist.github.com/piraveen/fafd0d984b2236e809d03a0e306c8a4d
	 * @param string $codeLanguage
	 * @return string
	 */
	public function getDefaultLanguage() {
	    $pathFile = storage_path("/json/languages.json");

	    if(!File::exists($pathFile)) {
	        return null;
	    }

	    $languages = File::get($pathFile);
	    $file = (array)json_decode(File::get($pathFile));
	    return ['en' => $file['en']]; // return default languages is English
	}

	/**
	 * FN Detect the line ending character of a csv file
	 * @param unknown $filePath
	 * @return mixed
	 */
	public static function getLineEndingCharacterCSV( $filePath ) {
	    // first, have PHP auto-detect the line endings, like @AbraCadaver suggested:
	    ini_set("auto_detect_line_endings", true);

	    // now open the file and read a single line from it
	    $file = fopen($filePath, 'r');
	    fgets($file);

	    // fgets() moves the pointer, so get the current position
	    $position = ftell($file);

	    // now get a couple bytes (here: 10) from around that position
	    fseek($file, $position - 5);
	    $data = fread($file, 10);

	    // we no longer need the file
	    fclose($file);

	    // now find out how many of each type EOL there are in those 10 bytes
	    // expected result is that two of these will be 0 and one will be 1
	    $eols = array(
	            "\r\n" => substr_count($data, "\r\n"),
	            "\r" => substr_count($data, "\r"),
	            "\n" => substr_count($data, "\n"),
	    );

	    // sort the EOL count in reverse order, so that the EOL with the highest
	    // count (expected: 1) will be the first item
	    arsort($eols);

	    // get the first item's key
	    $eol = key($eols);

	    return $eol;
	}

	/**
	 * FN ENCRYPT STRING
	 * @param string $string
	 * @return string
	 */
	public static function encryptData ( $string ) {
	    return Crypt::encrypt( $string );
	}

	/**
	 * FN DECRYPT STRING
	 * @param string $string
	 * @return string
	 */
	public static function DecryptData ( $string ) {
	    return Crypt::decryptString( $string );
	}

	public static function checkUserCanBeAddOrWithdrawCredit($user, $credit) {
	    if ($user->billing_type == "UNLIMITED") { // can ignore if user billing mode is UNLIMITED
	        return true;
	    }

	    if (round($credit, 2) > round($user->getBalance(), 2)) { // user cannot add credit more than current credit
	        return false;
	    } else {
	        return true;
	    }
	}

	/**
	 * FN Remove non-alphanumeric Characters
	 * @param string | array $input
	 */
	public static function removeNonAlphanumericCharacters( $input ) {
        if ( is_array( $input ) ) {
            $output = array();
            foreach ( $input as $data ) {
                $output[] = preg_replace('/[^A-Za-z0-9]/', '', $data);
            }
            return $output;
        } else {
            return preg_replace('/[^A-Za-z0-9]/', '', $input);
        }
	}

	public static function getDateTimeFormat($dateTime, $isTime = false) {
	    if ($dateTime == null) {
	        return "";
	    }

	    try {
	        $date=date_create($dateTime);
	        return date_format($date, $isTime ? config('app.datetime_format') : config('app.date_format'));
	    } catch (\Exception $e) {
	        return $dateTime;
	    }
	}

	/**
	 * compare date and now
	 * @param unknown $time
	 * @return boolean
	 */
	public function isExpired($time) {
	    return Carbon::now()->gt(Carbon::parse($time));
	}

	public function keyBase64() {
        return "TextsDaily@2017!";
	}
}
?>