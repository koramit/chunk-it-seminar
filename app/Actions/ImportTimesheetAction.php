<?php

namespace App\Actions;

use App\ExcelImports\TimesheetImport;
use App\Models\Timesheet;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;

class ImportTimesheetAction
{
    protected int $chunkSize = 100;

    protected array $filedMap = [
        'ภาควิชา/หน่วยงาน' => 'department',
        'หน่วยงาน' => 'division',
        'ประเภทบุคลากร' => 'type',
        'หมายเลขพนักงาน' => 'org_id',
        'ชื่อ - สกุล' => 'full_name',
        'ตำแหน่ง' => 'position',
        'เวลาปฏิบัติงาน' => 'work_hour',
        'หมายเหตุ Flex Time' => 'flex_time_note',
        'เวลาเข้า' => 'check_in',
        'เวลาออก' => 'check_out',
        'หมายเหตุ' => 'remark',
        'ชี้แจงเหตุผล' => 'reason',
        'สรุป' => 'summary',
        'วันที่ปฏิบัติงาน' => 'datestamp',
        'Flex Time (นาที)' => 'flex_time_use',
    ];

    public function fastExcel($file)
    {
        Timesheet::query()->truncate();
        $filePath = storage_path('app/import/'.$file.'.xlsx');

        (new FastExcel())->import($filePath, function ($row) {
            $model = [];
            foreach ($this->filedMap as $columnName => $fieldName) {
                $model[$fieldName] = $row[$columnName];
            }
            Timesheet::query()->create($model);
        });

        return true;
    }

    public function fastExcelChunk($file)
    {
        Timesheet::query()->truncate();
        $filePath = storage_path('app/import/'.$file.'.xlsx');

        $chunk = [];
        $now = now();
        (new FastExcel())->import($filePath, function ($row) use (&$chunk, $now) {
            $model = [];
            foreach ($this->filedMap as $columnName => $fieldName) {
                $model[$fieldName] = $row[$columnName];
            }
            $model['created_at'] = $now;
            $model['updated_at'] = $now;
            $chunk[] = $model;
            if (count($chunk) === $this->chunkSize) {
                Timesheet::query()->insert($chunk);
                $chunk = [];
            }
        });
        Timesheet::query()->insert($chunk);

        return true;
    }

    public function laravelExcel($file)
    {
        Timesheet::query()->truncate();

        $filePath = storage_path('app/import/'.$file.'.xlsx');

        Excel::import(new TimesheetImport($this->chunkSize), $filePath);

        return true;
    }

    public function chunkAeng($file)
    {
        $filePath = storage_path('app/import/'.$file.'.csv');

        if (!$handle = fopen($filePath, 'r')) {
            return false;
        }

        Timesheet::query()->truncate();
        $chunk = [];
        $now = now();
        $headRow = true;

        while ($row = fgetcsv($handle)) {
            if ($headRow) {
                $headRow = false;

                continue;
            }

            $model = [];
            $index = 0;
            foreach ($this->filedMap as $columnName => $fieldName) {
                $model[$fieldName] = $row[$index] !== '' ? $row[$index] : null;
                $index++;
            }

            $model['created_at'] = $now;
            $model['updated_at'] = $now;
            $chunk[] = $model;

            if (count($chunk) === $this->chunkSize) {
                Timesheet::query()->insert($chunk);
                $chunk = [];
                $now = now();
            }
        }

        fclose($handle);

        if (count($chunk)) {
            Timesheet::query()->insert($chunk);
        }

        return true;
    }
}