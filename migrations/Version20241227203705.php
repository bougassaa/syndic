<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241227203705 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE garage (id INT AUTO_INCREMENT NOT NULL, syndic_id INT NOT NULL, proprietaire_id INT NOT NULL, nom VARCHAR(100) NOT NULL, date_achat DATE NOT NULL, INDEX IDX_9F26610BF0654A02 (syndic_id), INDEX IDX_9F26610B76C50E4A (proprietaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE garage ADD CONSTRAINT FK_9F26610BF0654A02 FOREIGN KEY (syndic_id) REFERENCES syndic (id)');
        $this->addSql('ALTER TABLE garage ADD CONSTRAINT FK_9F26610B76C50E4A FOREIGN KEY (proprietaire_id) REFERENCES proprietaire (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE garage DROP FOREIGN KEY FK_9F26610BF0654A02');
        $this->addSql('ALTER TABLE garage DROP FOREIGN KEY FK_9F26610B76C50E4A');
        $this->addSql('DROP TABLE garage');
    }
}
