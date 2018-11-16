<?php

namespace App\Repositories\Clients;

interface IPriceConfigurationRepository {
    public function findWherePriceConfiguration($idUser, array $where, $columns = ['*']);
    public function updatePriceConfiguration($idUser, array $attributes, $id);
    public function createPriceConfiguration($idUser, array $attributes);
    public function allPriceConfiguration( $idUser );
    public function getDisabledCoutries($idUser);
    public function deletePriceConfiguration( $idUser, $id );
    public function importCSV($idClient, $filePath, $lineEndCharacter);
    public function getEnabledCoutries($idUser);
}