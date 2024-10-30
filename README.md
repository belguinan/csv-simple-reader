# CSV Simple Reader

A lightweight, zero-dependency PHP library for reading, writing, and exporting CSV files. Works with PHP 5.4 and above.

[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## Features

- 🚀 Simple and intuitive API
- 📖 Memory-efficient reading of large files
- 💾 Export data to CSV files
- ⬇️ Direct CSV downloads
- 🔒 Secure file handling
- 0️⃣ Zero dependencies
- ✅ PHP 5.4+ compatible

## Installation

Install via Composer:

```bash
composer require belguinan/csv-simple-reader
```

## Quick Start

```php
use Belguinan\CsvExporter;

// Initialize
$csv = new CsvExporter();

// Read CSV file
foreach ($csv->readFrom('path/to/file.csv') as $row) {
    var_dump($row);
}
```

## Usage Guide

### Reading CSV Files

```php
$csv = new CsvExporter();

// Read file line by line (memory efficient)
foreach ($csv->readFrom('input.csv') as $row) {
    // $row is an array containing the CSV columns
    var_dump($row);
}
```

### Creating CSV Files

```php
// Your data as array
$data = array(
    array('John', 'Doe', 'john@example.com'),
    array('Jane', 'Smith', 'jane@example.com')
);

// Optional headers
$headers = array('First Name', 'Last Name', 'Email');

// Create CSV exporter
$csv = new CsvExporter($data, $headers);

// Process and save
$csv->process()->save('output.csv');
```

### Downloading CSV Files

```php
// Create and force download
$csv = new CsvExporter($data, $headers);
$csv->process()->download('users-export');
```

### Chaining Operations

```php
// Process, download, and save in one go
$csv->process()
    ->download('export-file')
    ->save('backup/export.csv');
```

## Error Handling

The library throws `Exception` for various error conditions. It's recommended to wrap operations in try-catch blocks:

```php
try {
    $csv = new CsvExporter($data);
    $csv->process()->save('output.csv');
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

## Common Exceptions

- File not found
- File not readable
- Directory not writable
- Invalid CSV data structure
- Memory stream errors

## Best Practices

1. **Reading Large Files**
   ```php
   // Good - Memory efficient
   foreach ($csv->readFrom('large.csv') as $row) {
       processRow($row);
   }
   ```

2. **Setting Headers**
   ```php
   // Explicit headers
   $headers = array('ID', 'Name', 'Email');
   $csv = new CsvExporter($data, $headers);

   // Auto-generated headers from data keys
   $csv = new CsvExporter($data);
   ```

3. **Error Handling**
   ```php
   try {
       $csv->readFrom('file.csv');
   } catch (\Exception $e) {
       log_error($e->getMessage());
       // Handle error appropriately
   }
   ```

## Examples

### Export Users Table

```php
// Fetch users from database
$users = $db->query('SELECT id, name, email FROM users');

// Convert to array
$data = array();
while ($row = $users->fetch_assoc()) {
    $data[] = $row;
}

// Export
$csv = new CsvExporter($data);
$csv->process()->download('users-export');
```

### Process CSV in Chunks

```php
$csv = new CsvExporter();
$chunk = array();

foreach ($csv->readFrom('large-file.csv') as $index => $row) {
    $chunk[] = $row;
    
    // Process in chunks of 1000
    if (count($chunk) >= 1000) {
        processChunk($chunk);
        $chunk = array();
    }
}

// Process remaining rows
if (!empty($chunk)) {
    processChunk($chunk);
}
```

## License

MIT License - feel free to use this library in your projects.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Support

For bugs and feature requests, please use the [GitHub issue tracker](https://github.com/belguinan/csv-simple-reader/issues).