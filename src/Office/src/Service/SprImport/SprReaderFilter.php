<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 07.05.19
 * Time: 11:02
 */

namespace Office\Service\SprImport;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class SprReaderFilter implements IReadFilter
{
    /**
     * Ограничивает диапазон обрабатываемых значений в таблице
     *
     * @param string $column
     * @param int $row
     * @param string $worksheetName
     *
     * @return bool
     */
    public function readCell($column, $row, $worksheetName = '')
    {
        if ($column >= 0 && $column <= 5) {
            return true;
        }
        return false;
    }
}
