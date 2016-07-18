<?php
declare(strict_types = 1);

namespace ExpenseManagerCli\Storage;

interface DenormalizerInterface
{
    /**
     * @return object
     */
    public function denormalize(array $data);
}
