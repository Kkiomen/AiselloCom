<?php

namespace App\DTO;

/**
 * DTO wzbogaconych danych produktu.
 * DTO for enriched product data.
 *
 * Reprezentuje dane produktu po wzbogaceniu z web scraping.
 */
readonly class EnrichedProductDTO
{
    /**
     * Konstruktor.
     *
     * @param string $name Nazwa produktu
     * @param string $manufacturer Producent
     * @param float|null $price Cena (null jeśli nie podano)
     * @param string|null $description Opis
     * @param array $attributes Atrybuty produktu
     * @param array $sources URLs źródłowe
     * @param array $originalInput Oryginalne dane wejściowe
     * @param string|null $category Kategoria produktu
     * @param string|null $availability Status dostępności
     * @param array $images URLs zdjęć produktu
     * @param string|null $sku Numer SKU
     * @param string|null $gtin GTIN/EAN
     * @param float|null $rating Ocena produktu
     * @param float $serperCost Koszt zapytania do Serper API
     */
    public function __construct(
        public string $name,
        public string $manufacturer,
        public ?float $price,
        public ?string $description,
        public array $attributes,
        public array $sources,
        public array $originalInput,
        public ?string $category = null,
        public ?string $availability = null,
        public array $images = [],
        public ?string $sku = null,
        public ?string $gtin = null,
        public ?float $rating = null,
        public float $serperCost = 0.0,
    ) {
    }

    /**
     * Konwertuje do tablicy.
     * Converts to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'manufacturer' => $this->manufacturer,
            'price' => $this->price,
            'description' => $this->description,
            'attributes' => $this->attributes,
            'original_input' => $this->originalInput,
            'category' => $this->category,
            'availability' => $this->availability,
            'images' => $this->images,
            'sku' => $this->sku,
            'gtin' => $this->gtin,
            'rating' => $this->rating,
        ];
    }

    /**
     * Tworzy reprezentację tekstową dla AI.
     * Creates text representation for AI.
     *
     * @return string
     */
    public function toTextRepresentation(): string
    {
        $text = "Nazwa: {$this->name}\n";
        $text .= "Producent: {$this->manufacturer}\n";

        // Cena - tylko jeśli została podana
        if ($this->price !== null) {
            $text .= "Cena: {$this->price} PLN\n";
        }

        if ($this->category) {
            $text .= "Kategoria: {$this->category}\n";
        }

        if ($this->availability) {
            $availabilityLabels = [
                'in_stock' => 'Dostępny',
                'out_of_stock' => 'Niedostępny',
                'pre_order' => 'Przedsprzedaż',
                'discontinued' => 'Wycofany',
            ];
            $label = $availabilityLabels[$this->availability] ?? $this->availability;
            $text .= "Dostępność: {$label}\n";
        }

        if ($this->rating) {
            $text .= "Ocena: {$this->rating}/5\n";
        }

        if ($this->sku) {
            $text .= "SKU: {$this->sku}\n";
        }

        if ($this->description) {
            $text .= "Opis: {$this->description}\n";
        }

        if (!empty($this->attributes)) {
            $text .= "Atrybuty:\n";
            foreach ($this->attributes as $key => $value) {
                $text .= "- {$key}: {$value}\n";
            }
        }

        return $text;
    }
}
