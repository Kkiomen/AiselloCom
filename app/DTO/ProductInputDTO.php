<?php

namespace App\DTO;

/**
 * DTO danych wejściowych produktu.
 * DTO for product input data.
 *
 * Reprezentuje dane produktu dostarczone przez użytkownika API.
 */
readonly class ProductInputDTO
{
    /**
     * Konstruktor.
     *
     * @param string|null $name Nazwa produktu
     * @param string|null $manufacturer Producent
     * @param float|null $price Cena
     * @param string|null $description Opis
     * @param array|null $attributes Atrybuty produktu (key-value)
     * @param int|null $userPromptId ID customowego promptu użytkownika
     * @param bool $autoEnrich Czy automatycznie wzbogacać dane (true = droższe, lepsze opisy | false = tańsze, szybsze)
     * @param string $language Język w jakim ma być wygenerowany opis (default: pl)
     */
    public function __construct(
        public ?string $name = null,
        public ?string $manufacturer = null,
        public ?float $price = null,
        public ?string $description = null,
        public ?array $attributes = null,
        public ?int $userPromptId = null,
        public bool $autoEnrich = true,
        public string $language = 'pl',
    ) {
    }

    /**
     * Tworzy DTO z tablicy (np. z requestu).
     * Creates DTO from array (e.g., from request).
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            manufacturer: $data['manufacturer'] ?? null,
            price: isset($data['price']) ? (float) $data['price'] : null,
            description: $data['description'] ?? null,
            attributes: $data['attributes'] ?? null,
            userPromptId: isset($data['user_prompt_id']) ? (int) $data['user_prompt_id'] : null,
            autoEnrich: isset($data['auto_enrich']) ? (bool) $data['auto_enrich'] : true,
            language: $data['language'] ?? 'pl',
        );
    }

    /**
     * Identyfikuje brakujące pola.
     * Identifies missing fields.
     *
     * @return array Lista brakujących pól
     */
    public function getMissingFields(): array
    {
        $missing = [];

        if (empty($this->name)) {
            $missing[] = 'name';
        }
        if (empty($this->manufacturer)) {
            $missing[] = 'manufacturer';
        }
        if ($this->price === null) {
            $missing[] = 'price';
        }
        if (empty($this->description)) {
            $missing[] = 'description';
        }

        return $missing;
    }

    /**
     * Sprawdza czy są jakieś dane.
     * Checks if there's any data.
     *
     * @return bool
     */
    public function hasAnyData(): bool
    {
        return $this->name !== null
            || $this->manufacturer !== null
            || $this->price !== null
            || $this->description !== null
            || !empty($this->attributes);
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
            'user_prompt_id' => $this->userPromptId,
            'auto_enrich' => $this->autoEnrich,
            'language' => $this->language,
        ];
    }
}
