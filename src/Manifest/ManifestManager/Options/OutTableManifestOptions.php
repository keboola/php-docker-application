<?php

declare(strict_types=1);

namespace Keboola\Component\Manifest\ManifestManager\Options;

use function array_keys;
use function gettype;
use function is_array;

class OutTableManifestOptions
{
    /** @var string */
    private $destination;

    /** @var string[] */
    private $primaryKeyColumns;

    /** @var string[] */
    private $columns;

    /** @var bool */
    private $incremental;

    /** @var mixed[][] */
    private $metadata;

    /** @var mixed[][] */
    private $columnMetadata;

    /** @var string */
    private $delimiter;

    /** @var string */
    private $enclosure;

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        $result = [];
        if (isset($this->destination)) {
            $result['destination'] = $this->destination;
        }
        if (isset($this->primaryKeyColumns)) {
            $result['primary_key'] = $this->primaryKeyColumns;
        }
        if (isset($this->delimiter)) {
            $result['delimiter'] = $this->delimiter;
        }
        if (isset($this->enclosure)) {
            $result['enclosure'] = $this->enclosure;
        }
        if (isset($this->columns)) {
            $result['columns'] = $this->columns;
        }
        if (isset($this->incremental)) {
            $result['incremental'] = $this->incremental;
        }
        if (isset($this->metadata)) {
            $result['metadata'] = $this->metadata;
        }
        if (isset($this->columnMetadata)) {
            $result['column_metadata'] = $this->columnMetadata;
        }
        return $result;
    }

    public function setDestination(string $destination): OutTableManifestOptions
    {
        $this->destination = $destination;
        return $this;
    }

    /**
     * @param string[] $primaryKeyColumns
     * @return OutTableManifestOptions
     */
    public function setPrimaryKeyColumns(array $primaryKeyColumns): OutTableManifestOptions
    {
        $this->primaryKeyColumns = $primaryKeyColumns;
        return $this;
    }

    /**
     * @param string[] $columns
     * @return OutTableManifestOptions
     */
    public function setColumns(array $columns): OutTableManifestOptions
    {
        $this->columns = $columns;
        return $this;
    }

    public function setIncremental(bool $incremental): OutTableManifestOptions
    {
        $this->incremental = $incremental;
        return $this;
    }

    /**
     * @param mixed[] $metadata
     * @return OutTableManifestOptions
     */
    public function setMetadata(array $metadata): OutTableManifestOptions
    {
        foreach ($metadata as $value) {
            if (!is_array($value)) {
                throw new OptionsValidationException('Each metadata item must be an array');
            }
            $keys = array_keys($value);
            sort($keys);
            if ($keys !== ['key', 'value']) {
                throw new OptionsValidationException('Each metadata item must have only "key" and "value" keys');
            }
        }
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * @param mixed[] $columnsMetadata
     * @return OutTableManifestOptions
     */
    public function setColumnMetadata(array $columnsMetadata): OutTableManifestOptions
    {
        foreach ($columnsMetadata as $columnName => $columnMetadata) {
            if (!is_array($columnMetadata)) {
                throw new OptionsValidationException('Each column metadata item must be an array');
            }
            if (!is_string($columnName)) {
                throw new OptionsValidationException('Each column metadata item must have string key');
            }
            foreach ($columnMetadata as $key => $oneKeyAndValue) {
                if (!is_array($oneKeyAndValue)) {
                    throw new OptionsValidationException(sprintf(
                        'Column metadata item #%s for "%s" column must be an array, found "%s"',
                        $key,
                        $columnName,
                        gettype($oneKeyAndValue)
                    ));
                }
                $keys = array_keys($oneKeyAndValue);
                sort($keys);
                if ($keys !== ['key', 'value']) {
                    throw new OptionsValidationException(sprintf(
                        'Column metadata item #%s for "%s" column must have only "key" and "value" keys',
                        $key,
                        $columnName
                    ));
                }
            }
        }
        $this->columnMetadata = $columnsMetadata;
        return $this;
    }

    public function setDelimiter(string $delimiter): OutTableManifestOptions
    {
        $this->delimiter = $delimiter;
        return $this;
    }

    public function setEnclosure(string $enclosure): OutTableManifestOptions
    {
        $this->enclosure = $enclosure;
        return $this;
    }

    /**
     * @param mixed $metadata
     * @throws OptionsValidationException
     */
    private function validateMetadata($metadata): void
    {
        if (!is_array($metadata)) {
            throw new OptionsValidationException('Metadata must be an array');
        }
        foreach ($metadata as $key => $oneKeyAndValue) {
            if (!is_array($oneKeyAndValue)) {
                throw new OptionsValidationException(sprintf(
                    'Metadata item #%s must be an array, found "%s"',
                    $key,
                    gettype($oneKeyAndValue)
                ));
            }
            $keys = array_keys($oneKeyAndValue);
            sort($keys);
            if ($keys !== ['key', 'value']) {
                throw new OptionsValidationException(sprintf(
                    'Metadata item #%s must have only "key" and "value" keys',
                    $key
                ));
            }
        }
    }
}
