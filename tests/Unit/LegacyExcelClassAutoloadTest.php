<?php

namespace Tests\Unit;

use App\Exports\ExcelExports;
use App\Exports\ExportProduct;
use App\Imports\ExcelImports;
use App\Imports\ImportProduct;
use Tests\TestCase;

class LegacyExcelClassAutoloadTest extends TestCase
{
    public function test_legacy_excel_helpers_autoload_without_optional_excel_package(): void
    {
        $this->assertTrue(class_exists(ExcelExports::class));
        $this->assertTrue(class_exists(ExportProduct::class));
        $this->assertTrue(class_exists(ExcelImports::class));
        $this->assertTrue(class_exists(ImportProduct::class));
    }
}
