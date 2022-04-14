<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\MercadopagoSdk\Traits;

use Gpupo\CommonSchema\ORM\Entity\EntityInterface;
use Gpupo\CommonSdk\Exception\ManagerException;
use Symfony\Component\Console\Output\OutputInterface;

trait CsvFileProcessTrait
{
    protected $separator;

    protected function resolveKeysFromHeader($line, bool $cleanKeys = true)
    {
        $keys = [];

        $this->separator = ';';
        if (false != strpos($line, ',')) {
            $this->separator = ',';
        }

        foreach (str_getcsv($line, $this->separator) as $value) {
            if (!$cleanKeys) {
                $keys[] = mb_strtolower($value);
                continue;
            }

            $key = str_replace(
                ['mp_', 'reference'], 
                ['', 'id'], 
                mb_strtolower($value)
            );
            $keys[] = $key;
        }

        return $keys;
    }

    public function fetchCsvFileLines(EntityInterface $report, string $endpoint, OutputInterface $output = null): array
    {
        $map = $this->factorySimpleMap(['GET', sprintf('%s/%s', $endpoint, $report->getFileName())]);
        $destination = sprintf('var/cache/%s', $report->getFileName());

        if ($output) {
            $output->writeln(sprintf('Opening Report %s ...', $destination));
        }

        if (!file_exists($destination)) {
            if ($output) {
                $output->writeln(sprintf('Requesting remote Report %s ...', $destination));
            }
            $this->getClient()->downloadFile($map->getResource(), $destination);
        }

        $lines = file($destination, FILE_IGNORE_NEW_LINES);

        if (empty($lines)) {
            throw new ManagerException('Empty Report');
        }

        return $lines;
    }

    public function replaceKeysFromHeader(array $keys, array $mapFromNewKeys): array
    {
        return array_map(function($old_key) use ($mapFromNewKeys) {
            return $mapFromNewKeys[$old_key] ?? $old_key;
        }, $keys);
    }
}