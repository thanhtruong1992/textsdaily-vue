<?php

namespace App\Services\Settings;

use App\Services\BaseService;
use App\Repositories\Settings\IMobilePatternRepository;
use Illuminate\Support\Facades\File;

class MobilePatternService extends BaseService implements IMobilePatternService {
    /**
     */
    protected $mobilePatternRepo;

    /**
     *
     * @param IMobilePatternRepository $IMobilePatternRepo
     */
    public function __construct(IMobilePatternRepository $IMobilePatternRepo) {
        $this->mobilePatternRepo = $IMobilePatternRepo;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\IMobilePatternService::fetchAll()
     */
    public function fetchAll() {
        return $this->mobilePatternRepo->all ();
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\IMobilePatternService::getAllDataFormatDataTable()
     */
    public function getAllDataFormatDataTable($request) {
        $orderColumn = $request->get ( 'field' );
        $orderDirection = $request->get ( 'orderBy' );
        $keywork = $request->get ('search', '');
        $results = $this->mobilePatternRepo->getDataTableList($keywork, $orderColumn, $orderDirection);
        //
        return [
                'data' => $results->items(),
                'recordsTotal' => $results->total(),
                'recordsFiltered' => $results->total()
        ];
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\IMobilePatternService::getMobilePattern()
     */
    public function getMobilePattern($id) {
        return $this->mobilePatternRepo->find($id);
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\IMobilePatternService::updateMobilePattern()
     */
    public function updateMobilePattern(array $attributes, $id) {
        return $this->mobilePatternRepo->update ( $attributes, $id );
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\IMobilePatternService::importMobilePattern()
     */
    public function importMobilePattern($file, $path = '/public/settings/mobile-pattern') {
        if ($file->isValid ()) {
            // Upload file
            $fileName = time () . '.' . $file->getClientOriginalExtension ();
            $fileUploaded = $file->storeAs ( $path, $fileName );

            // Move data to database
            $filePath = addCslashes ( storage_path ( 'app/' . $fileUploaded ), '\\' );

            // Detect the line ending character of a csv file
            $lineEndCharacter = self::getLineEndingCharacterCSV($filePath);

            //
            $result = $this->mobilePatternRepo->importCSV ( $filePath, $lineEndCharacter );

            // Remove file
            File::delete ( storage_path ( 'app/' . $fileUploaded ) );

            //
            if ($result) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\IMobilePatternService::deleteMobilePattern()
     */
    public function deleteMobilePattern( $id ) {
        if (isset($id)) {
            $result = $this->mobilePatternRepo->delete($id);
            return $this->success($result);
        }
        return $this->fail();
    }
}