<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250101184628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE banque (id INT AUTO_INCREMENT NOT NULL, syndic_id INT NOT NULL, numero_banque VARCHAR(100) NOT NULL, rib VARCHAR(100) NOT NULL, label_compte VARCHAR(255) NOT NULL, agence VARCHAR(120) NOT NULL, UNIQUE INDEX UNIQ_B1F6CB3CF0654A02 (syndic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE banque ADD CONSTRAINT FK_B1F6CB3CF0654A02 FOREIGN KEY (syndic_id) REFERENCES syndic (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE banque DROP FOREIGN KEY FK_B1F6CB3CF0654A02');
        $this->addSql('DROP TABLE banque');
    }
}
