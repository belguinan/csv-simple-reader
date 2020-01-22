# CSV Simple Reader

This is a simple PHP class that allow you to **read, save, export and download** your array as **csv** with no dependencies.

## Install

```sh
composer require belguinan/csv-simple-reader
```

## Usage

```php
// Read csv file
$csv = new CsvExporter();
foreach ($csv->readFrom('test.csv') as $row) {
	var_dump($row);
}
```

```php
// your array to convert
$data = ..;
$csv = new CsvExporter($data, $headers);
```

```php
// Save your processed file.
$csv->process()->save('xxx/yyy/x.csv');
```

```php
// Download your file.
$csv->process()->download('fileName');
```

```php
// Download and save
$csv->process()->download('fileName')->save('xxxx/yyy/file.csv');
```

