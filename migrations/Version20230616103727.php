<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230616103727 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE InterventionMonteurs (id INT AUTO_INCREMENT NOT NULL, createdAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', start DATETIME NOT NULL, end DATETIME NOT NULL, code VARCHAR(255) NOT NULL, Libelle VARCHAR(255) NOT NULL, adresse VARCHAR(255) DEFAULT NULL, UserCr_id INT DEFAULT NULL, INDEX IDX_3B754F1724121FC0 (UserCr_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE interventionmonteurs_users (interventionmonteurs_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_6CA52678ACDF25C8 (interventionmonteurs_id), INDEX IDX_6CA5267867B3B43D (users_id), PRIMARY KEY(interventionmonteurs_id, users_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE InterventionMonteurs ADD CONSTRAINT FK_3B754F1724121FC0 FOREIGN KEY (UserCr_id) REFERENCES Users (id)');
        $this->addSql('ALTER TABLE interventionmonteurs_users ADD CONSTRAINT FK_6CA52678ACDF25C8 FOREIGN KEY (interventionmonteurs_id) REFERENCES InterventionMonteurs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interventionmonteurs_users ADD CONSTRAINT FK_6CA5267867B3B43D FOREIGN KEY (users_id) REFERENCES Users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE interventionmonteurs_users DROP FOREIGN KEY FK_6CA52678ACDF25C8');
        $this->addSql('DROP TABLE InterventionMonteurs');
        $this->addSql('DROP TABLE interventionmonteurs_users');
    }
}
