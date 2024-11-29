<?php

namespace App\Transformers;

use App\Exceptions\ValidateException;
use App\Models\Job;
use App\Solver\SolverClientFactory;
use App\Transformers\Roster\AmbulanceOfficeDataHandler;
use App\Transformers\School\SpreadSheetWithHeadersDataHandler;
use Illuminate\Support\Facades\App;

class SpreadSheetHandlerFactory
{
    /** @var bool feature flag */
    private bool $useHeadersVersion = false;

    public function __construct(bool $useHeadersVersion)
    {
        $this->useHeadersVersion = $useHeadersVersion;
    }

    public function createHandler($type, $fileName): SpreadSheetDataHandler
    {
        if ($type == Job::TYPE_SCHOOL) {
            if (!str_ends_with($fileName, '.xlsx')) {
                throw new ValidateException('We currently only handle xlsx file extensions for %s', $type);
            }

            if ($this->useHeadersVersion) {
                return new SpreadSheetWithHeadersDataHandler();
            }
            
            return new ExcelSchoolDataHandler();
        }

        if ($type == Job::TYPE_ROSTER) {
            if (!str_ends_with($fileName, '.xlsx')) {
                throw new ValidateException('We currently only handle xlsx file extensions for %s', $type);
            }

            return App::make(AmbulanceOfficeDataHandler::class);
        }

        throw new ValidateException(sprintf('Unimplemented job type %s', $type));
    }
}
