<?php

use Belguinan\CsvExporter;

/**
 * Usage :
 *
 * $csv = new CsvExporter($data, $headers);
 *
 * Save works like
 * $csv->process()->save('xxx/yyy/x.csv');
 *
 * Download:
 * $csv->process()->download('fileName');
 *
 * Download and save
 * $csv->process()->download('fileName')->save('xxxx/yyy/file.csv');
 *
 */

include 'vendor/autoload.php';

$csv = new CsvExporter();

foreach ($csv->readFrom('test.csv') as $row) {
	var_dump($row);
}
