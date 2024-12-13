<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241211101719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE appartement (id INT AUTO_INCREMENT NOT NULL, batiment_id INT NOT NULL, numero INT NOT NULL, INDEX IDX_71A6BD8DD6F6891B (batiment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE batiment (id INT AUTO_INCREMENT NOT NULL, syndic_id INT NOT NULL, nom VARCHAR(20) NOT NULL, INDEX IDX_F5FAB00CF0654A02 (syndic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cotisation (id INT AUTO_INCREMENT NOT NULL, appartement_id INT NOT NULL, proprietaire_id INT DEFAULT NULL, tarif_id INT NOT NULL, paid_at DATE NOT NULL, montant DOUBLE PRECISION NOT NULL, moyen_paiement VARCHAR(30) NOT NULL, INDEX IDX_AE64D2EDE1729BBA (appartement_id), INDEX IDX_AE64D2ED76C50E4A (proprietaire_id), INDEX IDX_AE64D2ED357C0A59 (tarif_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE depense (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, syndic_id INT NOT NULL, paid_at DATE NOT NULL, montant DOUBLE PRECISION NOT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_34059757C54C8C93 (type_id), INDEX IDX_34059757F0654A02 (syndic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proprietaire (id INT AUTO_INCREMENT NOT NULL, appartement_id INT NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, begin_at DATE NOT NULL COMMENT \'Date achat de l\'\'appartement\', leave_at DATE DEFAULT NULL COMMENT \'Date vente de l\'\'appartement\', INDEX IDX_69E399D6E1729BBA (appartement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE syndic (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tarif (id INT AUTO_INCREMENT NOT NULL, syndic_id INT NOT NULL, year INT NOT NULL, tarif DOUBLE PRECISION NOT NULL, INDEX IDX_E7189C9F0654A02 (syndic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_depense (id INT AUTO_INCREMENT NOT NULL, syndic_id INT NOT NULL, label VARCHAR(300) NOT NULL, montant DOUBLE PRECISION NOT NULL, frequence VARCHAR(50) DEFAULT NULL COMMENT \'Fréquence de dépenses (mensuelle, annuelle ou null pour ponctuelle)\', INDEX IDX_1C24F8A2F0654A02 (syndic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE appartement ADD CONSTRAINT FK_71A6BD8DD6F6891B FOREIGN KEY (batiment_id) REFERENCES batiment (id)');
        $this->addSql('ALTER TABLE batiment ADD CONSTRAINT FK_F5FAB00CF0654A02 FOREIGN KEY (syndic_id) REFERENCES syndic (id)');
        $this->addSql('ALTER TABLE cotisation ADD CONSTRAINT FK_AE64D2EDE1729BBA FOREIGN KEY (appartement_id) REFERENCES appartement (id)');
        $this->addSql('ALTER TABLE cotisation ADD CONSTRAINT FK_AE64D2ED76C50E4A FOREIGN KEY (proprietaire_id) REFERENCES proprietaire (id)');
        $this->addSql('ALTER TABLE cotisation ADD CONSTRAINT FK_AE64D2ED357C0A59 FOREIGN KEY (tarif_id) REFERENCES tarif (id)');
        $this->addSql('ALTER TABLE depense ADD CONSTRAINT FK_34059757C54C8C93 FOREIGN KEY (type_id) REFERENCES type_depense (id)');
        $this->addSql('ALTER TABLE depense ADD CONSTRAINT FK_34059757F0654A02 FOREIGN KEY (syndic_id) REFERENCES syndic (id)');
        $this->addSql('ALTER TABLE proprietaire ADD CONSTRAINT FK_69E399D6E1729BBA FOREIGN KEY (appartement_id) REFERENCES appartement (id)');
        $this->addSql('ALTER TABLE tarif ADD CONSTRAINT FK_E7189C9F0654A02 FOREIGN KEY (syndic_id) REFERENCES syndic (id)');
        $this->addSql('ALTER TABLE type_depense ADD CONSTRAINT FK_1C24F8A2F0654A02 FOREIGN KEY (syndic_id) REFERENCES syndic (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appartement DROP FOREIGN KEY FK_71A6BD8DD6F6891B');
        $this->addSql('ALTER TABLE batiment DROP FOREIGN KEY FK_F5FAB00CF0654A02');
        $this->addSql('ALTER TABLE cotisation DROP FOREIGN KEY FK_AE64D2EDE1729BBA');
        $this->addSql('ALTER TABLE cotisation DROP FOREIGN KEY FK_AE64D2ED76C50E4A');
        $this->addSql('ALTER TABLE cotisation DROP FOREIGN KEY FK_AE64D2ED357C0A59');
        $this->addSql('ALTER TABLE depense DROP FOREIGN KEY FK_34059757C54C8C93');
        $this->addSql('ALTER TABLE depense DROP FOREIGN KEY FK_34059757F0654A02');
        $this->addSql('ALTER TABLE proprietaire DROP FOREIGN KEY FK_69E399D6E1729BBA');
        $this->addSql('ALTER TABLE tarif DROP FOREIGN KEY FK_E7189C9F0654A02');
        $this->addSql('ALTER TABLE type_depense DROP FOREIGN KEY FK_1C24F8A2F0654A02');
        $this->addSql('DROP TABLE appartement');
        $this->addSql('DROP TABLE batiment');
        $this->addSql('DROP TABLE cotisation');
        $this->addSql('DROP TABLE depense');
        $this->addSql('DROP TABLE proprietaire');
        $this->addSql('DROP TABLE syndic');
        $this->addSql('DROP TABLE tarif');
        $this->addSql('DROP TABLE type_depense');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
