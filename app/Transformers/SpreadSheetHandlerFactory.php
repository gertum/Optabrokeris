<?php

namespace App\Transformers;

use App\Exceptions\ValidateException;
use App\Solver\SolverClientFactory;

class SpreadSheetHandlerFactory
{
    public function createHandler($type, $fileName): SpreadSheetDataHandler
    {
        if (!str_ends_with($fileName, '.xlsx')) {
            throw new ValidateException('We currently only handle xlsx file extensions');
        }

        if ($type == SolverClientFactory::TYPE_SCHOOL) {
            return new ExcelSchoolDataHandler();
        }

        throw new ValidateException(sprintf('Unimplemented job type %s', $type));
    }
}
