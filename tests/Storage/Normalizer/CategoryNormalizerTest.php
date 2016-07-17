<?php
declare(strict_types = 1);

namespace Tests\ExpenseManagerCli\Storage\Normalizer;

use ExpenseManagerCli\{
    Storage\NormalizerInterface,
    Storage\Normalizer\CategoryNormalizer,
    Entity\Category\Identity
};
use ExpenseManager\{
    Entity\Category,
    Color
};
use Ramsey\Uuid\Uuid;

class CategoryNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NormalizerInterface::class,
            new CategoryNormalizer
        );
    }

    public function testNormalize()
    {
        $normalizer = new CategoryNormalizer;
        $category = new Category(
            new Identity($uuid = (string) Uuid::uuid4()),
            'foo',
            new Color('white')
        );

        $pair = $normalizer->normalize($category);
        $this->assertSame($uuid, $pair->key());
        $this->assertSame(
            [
                'identity' => $uuid,
                'name' => 'foo',
                'color' => 'white',
            ],
            $pair->value()
        );
    }

    /**
     * @expectedException ExpenseManagerCli\Exception\InvalidArgumentException
     */
    public function testThrowWhenTryingToNormalizeWrongObject()
    {
        (new CategoryNormalizer)->normalize(new \stdClass);
    }
}
