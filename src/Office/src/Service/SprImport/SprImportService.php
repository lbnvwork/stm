<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 07.05.19
 * Time: 9:58
 */

namespace Office\Service\SprImport;

use Zend\Diactoros\UploadedFile;

/**
 * Class SprImportService
 * Импорт из таблицы
 *
 * @package Office\Service\SprImport
 */
class SprImportService
{
    /**
     * Возвращает массив данных, полученных из Xlsx таблицы
     *
     * @param UploadedFile $file
     *
     * @return array|null
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function getTableArr(UploadedFile $file, string $range): ?array
    {
        $streamData = $file->getStream()->getMetadata();
        $fileName = $streamData['uri'];
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        $reader->setReadFilter(new SprReaderFilter());
        try {
            $spreadsheet = $reader->load($fileName);
        } catch (\Exception $e) {
            return null;
        }
        $sheet = $spreadsheet->getSheet(0);
        $range = $sheet->rangeToArray($range.$sheet->getHighestDataRow());
        return $this->unsetNullRows($range);
    }

    private function unsetNullRows(array $table)
    {
        foreach ($table as $rowIndex => $row) {
            $nullRow = true;
            foreach ($table[$rowIndex] as $item) {
                if ($item!==null) {
                    $nullRow = false;
                    break;
                }
            }
            if ($nullRow) {
                unset($table[$rowIndex]);
            }
        }
        return $table;
    }
}
