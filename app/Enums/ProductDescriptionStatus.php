<?php

namespace App\Enums;

/**
 * Enum statusu generowania opisu produktu.
 *
 * Reprezentuje możliwe statusy w cyklu życia generowania opisu.
 */
enum ProductDescriptionStatus: string
{
    /**
     * Oczekujący - opis czeka na przetworzenie.
     * Pending - description is waiting to be processed.
     */
    case PENDING = 'pending';

    /**
     * Przetwarzanie - opis jest obecnie generowany.
     * Processing - description is currently being generated.
     */
    case PROCESSING = 'processing';

    /**
     * Zakończony - opis został pomyślnie wygenerowany.
     * Completed - description was successfully generated.
     */
    case COMPLETED = 'completed';

    /**
     * Niepowodzenie - generowanie opisu nie powiodło się.
     * Failed - description generation failed.
     */
    case FAILED = 'failed';

    /**
     * Zwraca czytelną etykietę dla statusu.
     * Returns human-readable label for the status.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('api.status.pending'),
            self::PROCESSING => __('api.status.processing'),
            self::COMPLETED => __('api.status.completed'),
            self::FAILED => __('api.status.failed'),
        };
    }

    /**
     * Zwraca kolor dla statusu (do użycia w UI).
     * Returns color for the status (for UI use).
     *
     * @return string
     */
    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::PROCESSING => 'blue',
            self::COMPLETED => 'green',
            self::FAILED => 'red',
        };
    }

    /**
     * Sprawdza czy status jest finalny (zakończony lub failed).
     * Checks if the status is final (completed or failed).
     *
     * @return bool
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::COMPLETED, self::FAILED]);
    }

    /**
     * Sprawdza czy status oznacza sukces.
     * Checks if the status indicates success.
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this === self::COMPLETED;
    }
}
