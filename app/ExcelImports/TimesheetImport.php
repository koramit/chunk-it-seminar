<?php

namespace App\ExcelImports;

use App\Models\Timesheet;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class TimesheetImport implements ToModel, WithBatchInserts, WithChunkReading
{
    protected int $chunkSize;

    public function __construct($chunkSize)
    {
        $this->chunkSize = $chunkSize;
    }

    public function model(array $row)
    {
        if ($row[0] === 'ภาควิชา/หน่วยงาน') {
            return null;
        }

        return new Timesheet([
            'department' => $row[0],
            'division' => $row[1],
            'type' => $row[2],
            'org_id' => $row[3],
            'full_name' => $row[4],
            'position' => $row[5],
            'work_hour' => $row[6],
            'flex_time_note' => $row[7],
            'check_in' => $row[8],
            'check_out' => $row[9],
            'remark' => $row[10],
            'reason' => $row[11],
            'summary' => $row[12],
            'datestamp' => Carbon::create($row[13]),
            'flex_time_use' => $row[14],
        ]);
    }

    public function batchSize(): int
    {
        return $this->chunkSize;
    }

    public function chunkSize(): int
    {
        return $this->chunkSize;
    }

}