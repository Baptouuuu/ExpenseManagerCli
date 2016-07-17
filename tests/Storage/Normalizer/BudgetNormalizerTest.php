<?php
declare(strict_types = 1);

namespace Tests\ExpenseManagerCli\Storage\Normalizer;

use ExpenseManagerCli\{
    Storage\NormalizerInterface,
    Storage\Normalizer\BudgetNormalizer,
    Entity\Budget\Identity,
    Entity\Category\Identity as Category
};
use ExpenseManager\{
    Entity\Budget,
    Entity\Category\IdentityInterface,
    Amount
};
use Innmind\Immutable\Set;
use Ramsey\Uuid\Uuid;

class BudgetNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NormalizerInterface::class,
            new BudgetNormalizer
        );
    }

    public function testNormalize()
    {
        $normalizer = new BudgetNormalizer;
        $budget = new Budget(
            new Identity($uuid = (string) Uuid::uuid4()),
            'foo',
            new Amount(42),
            (new Set(IdentityInterface::class))
                ->add(new Category($cat = (string) Uuid::uuid4()))
        );

        $pair = $normalizer->normalize($budget);
        $this->assertSame($uuid, $pair->key());
        $this->assertSame(
            [
                'identity' => $uuid,
                'name' => 'foo',
                'amount' => 42,
                'categories' => [$cat],
            ],
            $pair->value()
        );
    }

    /**
     * @expectedException ExpenseManagerCli\Exception\InvalidArgumentException
     */
    public function testThrowWhenTryingToNormalizeWrongObject()
    {
        (new BudgetNormalizer)->normalize(new \stdClass);
    }
}
