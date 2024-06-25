<?php

namespace App\Console\Commands;

use App\Domain\Roster\Hospital\ScheduleParser;
use Illuminate\Console\Command;

class HospitalExcel2Json extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hospital:excel2json {excelFile}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parses the given excel file and returns parsed json result.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $excelFile = $this->argument('excelFile');
//        $this->output->writeln(sprintf('Parsing excel file [%s] ...', $excelFile ));
        $scheduleParser = new ScheduleParser();

        $schedule = $scheduleParser->parseScheduleXls($excelFile, ScheduleParser::createHospitalTimeSlices());

        $this->output->writeln( json_encode($schedule) );
    }
}
