<?php
class OrderStatus {

    /*Confirmé
Numéro incorrect
Annulé
Rappel
Reporter
Occupé
Pas réponse 1
Pas réponse 2
Pas reponse 3 + SMS
Fake
Double*/
    // Constantes pour les statuts
    public const EN_ATTENTE = 'En attente';
    public const CONFIRME = 'Confirmé';
    public const PNUMERO = 'Numéro incorrect';
    public const RAPPEL = 'Rappel';
    public const REPORTER = 'Reporter';
    public const OCCUPE = 'Occupé';
    public const PASR1 = 'Pas réponse 1';
    public const PASR2 = 'Pas réponse 2';
    public const PASRSMS = 'Pas reponse 3';
    public const FAKE = 'Fake';
    public const DOUBLE = 'Double';
    public const ANNULEE = 'Annulée';
     public const EN_COURS = 'En Cours Expédié';
    public const EXPEDIE = 'Expédié';

    // Couleurs associées aux statuts
    private const STATUS_COLORS = [
        self::EN_ATTENTE => 'warning',    // jaune/warning
        self::CONFIRME => 'success',         // bleu/info
        self::PNUMERO => 'warning',
        self::RAPPEL => 'info',    // jaune/warning
        self::REPORTER => 'primary',         // bleu/info
        self::OCCUPE => 'warning',
        self::PASR1 => 'warning',    // jaune/warning
        self::PASR2 => 'warning',         // bleu/info
        self::PASRSMS => 'warning',            // vert/success
        self::FAKE => 'dark',            // vert/success
        self::DOUBLE => 'dark',            // vert/success          // vert/success
        self::ANNULEE => 'danger',         // rouge/danger
                self::EN_COURS => 'info',     // bleu pour en cours
        self::EXPEDIE => 'success'    // vert pour expédié
    ];

    // Icônes associées aux statuts (Font Awesome)
    private const STATUS_ICONS = [
        self::EN_ATTENTE => 'clock',
        self::CONFIRME => 'check-circle',
        self::PNUMERO => 'bell-slash',
        self::RAPPEL => 'clock',
        self::REPORTER => 'bell',
        self::OCCUPE => 'bell-slash',
        self::PASR1 => 'bell-slash',
        self::PASR2 => 'bell-slash',
        self::PASRSMS => 'bell-slash',
        self::FAKE => 'ban',
        self::DOUBLE => 'mobile',
        self::ANNULEE => 'times',
        self::EN_COURS => 'clock',
        self::EXPEDIE => 'truck'
    ];

    /**
     * Récupère tous les statuts disponibles
     * @return array Liste des statuts
     */
    public static function getAllStatuses(): array {
        return [
            self::EN_ATTENTE,
            self::CONFIRME,
            self::PNUMERO,
            self::RAPPEL,
            self::REPORTER,
            self::OCCUPE,
            self::PASR1,
            self::PASR2,
            self::PASRSMS,
            self::FAKE,
            self::DOUBLE,
            self::ANNULEE,
            self::EN_COURS,
            self::EXPEDIE
        ];
    }

    /**
     * Récupère la couleur associée à un statut
     * @param string $status Le statut
     * @return string La classe de couleur Bootstrap
     */
    public static function getStatusColor(string $status): string {
        return self::STATUS_COLORS[$status] ?? 'secondary';
    }

    /**
     * Récupère l'icône associée à un statut
     * @param string $status Le statut
     * @return string L'icône Font Awesome
     */
    public static function getStatusIcon(string $status): string {
        return self::STATUS_ICONS[$status] ?? 'question';
    }

    /**
     * Génère le badge HTML pour un statut donné
     * @param string $status Le statut
     * @return string Le badge HTML
     */
    public static function getStatusBadge(string $status): string {
        $color = self::getStatusColor($status);
        $icon = self::getStatusIcon($status);
        return sprintf(
            '<span class="badge badge-%s"><i class="fas fa-%s mr-1"></i>%s</span>',
            $color,
            $icon,
            htmlspecialchars($status)
        );
    }

    /**
     * Vérifie si un statut est valide
     * @param string $status Le statut à vérifier
     * @return bool
     */
    public static function isValidStatus(string $status): bool {
        return in_array($status, self::getAllStatuses());
    }

    /**
     * Récupère les options pour un select HTML
     * @param string $selectedStatus Le statut sélectionné (optionnel)
     * @return string Options HTML
     */
    public static function getStatusSelectOptions(string $selectedStatus = ''): string {
        $options = [];
        foreach (self::getAllStatuses() as $status) {
            $selected = $status === $selectedStatus ? ' selected' : '';
            $options[] = sprintf(
                '<option value="%s"%s>%s</option>',
                $status,
                $selected,
                $status
            );
        }
        return implode("\n", $options);
    }

        /**
     * Récupère uniquement les statuts de suivi d'expédition
     * @return array Liste des statuts de suivi
     */
    public static function getTrackingStatuses(): array {
        return [
            self::EN_COURS,
        ];
    }
}