<?php
namespace App\Services\ShortLinks;

interface IShortLinkService {
    public function shortLink($link);
    public function shortLinkDCT($data);
}