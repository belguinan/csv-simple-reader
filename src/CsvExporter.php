<?php

namespace Belguinan;

/**
 * CsvExporter - A simple class to handle CSV operations
 */
class CsvExporter
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @var string|null
     */
    protected $csvResult;

    /**
     * @var string
     */
    protected $delimiter = ',';

    /**
     * @var string
     */
    protected $enclosure = '"';

    /**
     * @var string
     */
    protected $escape = '\\';

    /**
     * @param array $data Data to export
     * @param array $headers CSV headers
     */
    public function __construct($data = array(), $headers = array())
    {
        $this->data = $data;
        $this->headers = $headers;
    }

    /**
     * Read CSV file line by line using a generator
     *
     * @param string $path File path to read
     * @return \Generator
     * @throws \Exception
     */
    public function readFrom($path)
    {
        if (!file_exists($path)) {
            throw new \Exception(sprintf('File not found: %s', $path));
        }

        if (!is_readable($path)) {
            throw new \Exception(sprintf('File is not readable: %s', $path));
        }

        $file = @fopen($path, 'r');
        if ($file === false) {
            throw new \Exception(sprintf('Could not open file: %s', $path));
        }

        $exception = null;

        try {
            while (($row = fgetcsv($file, 0, $this->delimiter, $this->enclosure)) !== false) {
                yield $row;
            }
        } catch (\Exception $e) {
            $exception = $e;
        }

        fclose($file);

        if ($exception !== null) {
            throw $exception;
        }
    }

    /**
     * Convert data to CSV format
     *
     * @return self
     * @throws \Exception
     */
    public function process()
    {
        if (count($this->data) === 0) {
            $this->csvResult = '';
            return $this;
        }

        $output = fopen('php://temp', 'r+');

        if ($output === false) {
            throw new \Exception('Failed to open temporary memory stream');
        }

        $headers = $this->headers;

        $headersCount = count($headers);

        if (!is_array($headers) || $headersCount === 0) {
            $headers = array_keys(reset($this->data));
        }

        if ($headersCount) {
            if (fputcsv($output, $headers, $this->delimiter, $this->enclosure) === false) {
                fclose($output);
                throw new \Exception('Failed to write CSV headers');
            }
        }

        foreach ($this->data as $row) {
            if (!is_array($row)) {
                continue;
            }
            if (fputcsv($output, $row, $this->delimiter, $this->enclosure) === false) {
                fclose($output);
                throw new \Exception('Failed to write CSV data');
            }
        }

        rewind($output);
        $this->csvResult = stream_get_contents($output);
        fclose($output);

        if ($this->csvResult === false) {
            throw new \Exception('Failed to get CSV content from stream');
        }

        return $this;
    }

    /**
     * Save CSV to a file
     *
     * @param string $path
     * @return self
     * @throws \Exception
     */
    public function save($path)
    {
        if ($this->isEmpty()) {
            $this->process();
        }

        $dir = dirname($path);
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0777, true)) {
                throw new \Exception(sprintf('Directory does not exist and could not be created: %s', $dir));
            }
        }

        if (file_exists($path) && !is_writable($path)) {
            throw new \Exception(sprintf('File exists but is not writable: %s', $path));
        }

        if (@file_put_contents($path, $this->getCsvResult()) === false) {
            throw new \Exception(sprintf('Failed to save file: %s', $path));
        }

        return $this;
    }

    /**
     * Download CSV file
     *
     * @param string $fileName
     * @return self
     */
    public function download($fileName = 'output')
    {
        if ($this->isEmpty()) {
            $this->process();
        }

        $fileName = $this->sanitizeFilename($fileName);
        
        $headers = array(
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $fileName . '.csv',
            'Expires'            => '0',
            'Pragma'             => 'public'
        );

        foreach ($headers as $key => $value) {
            header($key . ': ' . $value);
        }

        echo $this->getCsvResult();

        return $this;
    }

    /**
     * Set CSV string result
     *
     * @param string $csvResult
     * @return void
     */
    public function setCsvResult($csvResult)
    {
        $this->csvResult = $csvResult;
    }

    /**
     * Get CSV string result
     *
     * @return string
     */
    public function getCsvResult()
    {
        return $this->csvResult;
    }

    /**
     * Reset CSV content
     *
     * @return self
     */
    public function reset()
    {
        $this->csvResult = '';

        return $this;
    }

    /**
     * Get data array
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Check if CSV result is empty
     *
     * @return boolean
     */
    protected function isEmpty()
    {
        return !isset($this->csvResult) || strlen(trim($this->csvResult)) === 0;
    }

    /**
     * Sanitize filename to prevent directory traversal and invalid characters
     *
     * @param string $fileName
     * @return string
     */
    protected function sanitizeFilename($fileName)
    {
        $fileName = str_replace(array('/', '\\', '..', "\0"), '', $fileName);

        $fileName = preg_replace('/[^a-zA-Z0-9\-_]/', '-', $fileName);

        return empty($fileName) ? 'csvExport' : $fileName;
    }
}