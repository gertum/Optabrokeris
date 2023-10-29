<?php

namespace App\Transformers;

use App\Exceptions\ValidateException;
use App\Solver\SolverClientFactory;
use App\Transformers\School\SpreadSheetWithHeadersDataHandler;

class SpreadSheetHandlerFactory
{
    private bool $useHeadersVersion = false;

    public function __construct(bool $useHeadersVersion)
    {
        $this->useHeadersVersion = $useHeadersVersion;
    }

    public function createHandler($type, $fileName): SpreadSheetDataHandler
    {
        if ($type == SolverClientFactory::TYPE_SCHOOL) {
            if (!str_ends_with($fileName, '.xlsx')) {
                throw new ValidateException('We currently only handle xlsx file extensions for %s', $type);
            }

            if ($this->useHeadersVersion) {
                return new SpreadSheetWithHeadersDataHandler();
            }
            return new ExcelSchoolDataHandler();
        }

        throw new ValidateException(sprintf('Unimplemented job type %s', $type));
    }
}
