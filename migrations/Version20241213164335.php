<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241213164335 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE type_depense CHANGE frequence frequence VARCHAR(50) DEFAULT NULL COMMENT \'Fréquence de dépenses (mensuelle, annuelle, occasionnelle)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE type_depense CHANGE frequence frequence VARCHAR(50) DEFAULT NULL COMMENT \'Fréquence de dépenses (mensuelle, annuelle ou null pour ponctuelle)\'');
    }
}
