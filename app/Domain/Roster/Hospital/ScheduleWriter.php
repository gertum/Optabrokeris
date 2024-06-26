<?php

namespace App\Domain\Roster\Hospital;

use alexandrainst\XlsxFastEditor\XlsxFastEditor;
use App\Domain\Roster\Schedule;
use Shuchkin\SimpleXLSXGen;

class ScheduleWriter
{
    public function writeSchedule(string $fileTemplate, Schedule $schedule, string $outputFile)
    {
        $wrapper = ExcelWrapper::parse($fileTemplate);
//        $xlsxGen = new SimpleXLSXGen();
//        // šitas sprendimas netinka, reikia kitos bibliotekos
//        // gal šita tiktų : alexandrainst/php-xlsx-fast-editor
//        $sheets = $wrapper->getXlsx()->getSheets();
//        $xlsxGen->addSheet($wrapper->getXlsx()->rows(), $sheets[0]->getName());
//
//        // TODO fill values from $schedule
//
//        $xlsxGen->setAuthor('Inkodus');
//        $xlsxGen->setCompany('Inkodus');
//        $xlsxGen->setManager('Inkodus');
//        $xlsxGen->setLastModifiedBy('Inkodus');
//
//        $xlsxGen->saveAs($outputFile);

        copy($fileTemplate, $outputFile);

        // $wrapper must tell where is the cell we need to edit

        $xlsxFastEditor = new XlsxFastEditor($outputFile);

        $worksheetName = $xlsxFastEditor->getWorksheetName(1);
        $worksheetId1 = $xlsxFastEditor->getWorksheetNumber($worksheetName);

        $xlsxFastEditor->writeString($worksheetId1,'H10', 'test');

        $xlsxFastEditor->save();
    }
}