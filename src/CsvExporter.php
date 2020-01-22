<?php

namespace App;

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
     * @var mixed
     */
    protected $csvResult;

    /**
     * @param array $data  data to export
     * @param mixed $headers csv headers
     */
    public function __construct(array $data = [], array $headers = [])
    {
        $this->data = $data;
        $this->headers = $headers;
    }

    /**
     * @param  string $path
     * @return \Generator
     */
    public function readFrom($path)
    {
        $file = fopen($path, 'r');

        while (($row = fgetcsv($file)) !== false) {
            yield $row;
        }

        fclose($file);
    }

    /**
     * Set csv string
     *
     * @param mixed $csvResult csv string result
     */
    public function setCsvResult($csvResult)
    {
        $this->csvResult = $csvResult;
    }

    /**
     * @return mixed
     */
    public function getCsvResult()
    {
        return $this->csvResult;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return boolean
     */
    protected function isEmpty()
    {
        return strlen(trim($this->csvResult)) === 0;
    }

    /**
     * Save csv to a path
     *
     * @param  string $path
     * @return $this
     */
    public function save(string $path)
    {
        if ($this->isEmpty()) {
            $this->process();
        }

        file_put_contents($path, $this->getCsvResult());

        return $this;
    }

    /**
     * Convert data to csv
     *
     * @return self
     */
    public function process()
    {
        ob_start();

        $fh = fopen("php://output", "w");

        $headers = $this->headers;

        if (! is_array($this->headers)) {
            $headers = array_keys($this->data[0]);
        }

        fputcsv($fh, $headers);

        foreach ($this->data as $row) {
            fputcsv($fh, $row);
        }

        $string = ob_get_clean();

        ob_end_clean();

        $this->setCsvResult($string);

        return $this;
    }

    /**
     * Get csv file
     *
     * @param string $fileName
     * @return self
     */
    public function download($fileName = "csvExport"): self
    {
        if ($this->isEmpty()) {
            $this->process();
        }

        $htmlHeaders = [
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$fileName}.csv",
            "Expires"             => "0",
            "Pragma"              => "public",
        ];

        foreach ($htmlHeaders as $key => $value) {
            header($key.": ".$value);
        }

        echo $this->getCsvResult();

        return $this;
    }
}
