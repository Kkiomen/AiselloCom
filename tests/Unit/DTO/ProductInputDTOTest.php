<?php

namespace Tests\Unit\DTO;

use App\DTO\ProductInputDTO;
use Tests\TestCase;

class ProductInputDTOTest extends TestCase
{
    /**
     * Test tworzenia DTO z tablicy.
     */
    public function test_from_array(): void
    {
        $data = [
            'name' => 'Test Product',
            'manufacturer' => 'Test Brand',
            'price' => 99.99,
            'description' => 'Test description',
            'attributes' => ['color' => 'red'],
        ];

        $dto = ProductInputDTO::fromArray($data);

        $this->assertEquals('Test Product', $dto->name);
        $this->assertEquals('Test Brand', $dto->manufacturer);
        $this->assertEquals(99.99, $dto->price);
    }

    /**
     * Test identyfikacji brakujących pól.
     */
    public function test_get_missing_fields(): void
    {
        $dto = new ProductInputDTO(name: 'Product', manufacturer: null);

        $missing = $dto->getMissingFields();

        $this->assertContains('manufacturer', $missing);
        $this->assertContains('price', $missing);
        $this->assertContains('description', $missing);
    }

    /**
     * Test sprawdzania czy są dane.
     */
    public function test_has_any_data(): void
    {
        $dto1 = new ProductInputDTO(name: 'Product');
        $this->assertTrue($dto1->hasAnyData());

        $dto2 = new ProductInputDTO();
        $this->assertFalse($dto2->hasAnyData());
    }
}
