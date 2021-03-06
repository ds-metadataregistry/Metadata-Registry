<?php

/** Created by PhpStorm,  User: jonphipps,  Date: 2017-05-05,  Time: 5:57 PM */

namespace App\Services\Import;

use Arcanedev\NoCaptcha\Exceptions\InvalidUrlException;
use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;
use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_Sheet;
use Illuminate\Support\Collection;
use function base_path;
use function collect;

class GoogleSpreadsheet
{
    private $service;
    private $spreadsheetId;

    /** Google spreadsheet object has
     * a google services connection <— built with service
     * a URL <— input in constructor
     * an identifier <— gets from the URL
     * a collection of worksheets <— gets from service.
     *
     * @param string $sheetUrl
     */
    public function __construct(string $sheetUrl)
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . base_path('client_secret.json'));

        $client = new Google_Client;
        $client->useApplicationDefaultCredentials();
        $client->setApplicationName('Open Metadata Registry Import');
        $client->setScopes(['https://www.googleapis.com/auth/drive', 'https://spreadsheets.google.com/feeds']);

        $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];
        ServiceRequestFactory::setInstance(new DefaultServiceRequest($accessToken));
        $this->service       = new Google_Service_Sheets($client);
        $this->spreadsheetId = $this->getIdFromUrl($sheetUrl);
    }

    public function getWorksheetData($name)
    {
        return collect($this->service->spreadsheets_values->get($this->spreadsheetId, $name)->getValues());
    }

    public function getSpreadsheetTitle(): string
    {
        return $this->service->spreadsheets->get($this->spreadsheetId)->getProperties()->getTitle();
    }

    /**
     * @return Collection
     */
    public function getWorksheets(): Collection
    {
        $sheets     = [];
        $worksheets = $this->service->spreadsheets->get($this->spreadsheetId)->sheets;
        /** @var Google_Service_Sheets_Sheet[] $worksheets */
        foreach ($worksheets as $worksheet) {
            $sheets[] = $worksheet->getProperties()->title;
        }

        return collect($sheets);
    }

    /**
     * @param string $url Google service URL
     *
     * @return string
     * @throws \Arcanedev\NoCaptcha\Exceptions\InvalidUrlException
     */
    private function getIdFromUrl(string $url): string
    {
        preg_match('/[-\\w]{25,}/u', $url, $matches);
        if (count($matches)) {
            return $matches[0];
        }
        throw new InvalidUrlException();
    }
}
