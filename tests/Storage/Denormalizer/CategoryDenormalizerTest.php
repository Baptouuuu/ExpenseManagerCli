<?php
declare(strict_types = 1);

namespace Tests\ExpenseManager\Cli\Storage\Denormalizer;

use ExpenseManager\Cli\Storage\{
    Denormalizer\CategoryDenormalizer,
    DenormalizerInterface
};
use ExpenseManager\Entity\Category;
use Ramsey\Uuid\Uuid;

class CategoryDenormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            DenormalizerInterface::class,
            new CategoryDenormalizer
        );
    }

    public function testDenormalize()
    {
        $category = (new CategoryDenormalizer)->denormalize([
            'identity' => $identity = (string) Uuid::uuid4(),
            'name' => 'foo',
            'color' => 'white',
        ]);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertSame($identity, (string) $category->identity());
        $this->assertSame('foo', $category->name());
        $this->assertSame('white', (string) $category->color());
        $this->assertCount(0, $category->recordedEvents());
    }
}
