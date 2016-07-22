<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Storage;

interface DenormalizerInterface
{
    /**
     * @return object
     */
    public function denormalize(array $data);
}
