<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230622084052 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE interventionmonteurs_affairepiece');
        $this->addSql('ALTER TABLE affairepiece DROP FOREIGN KEY FK_50D0B71A2A43AA1F');
        $this->addSql('DROP INDEX IDX_50D0B71A2A43AA1F ON affairepiece');
        $this->addSql('ALTER TABLE affairepiece DROP interventionMonteurs_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE interventionmonteurs_affairepiece (interventionmonteurs_id INT NOT NULL, affairepiece_id INT NOT NULL, INDEX IDX_B8B10ACFACDF25C8 (interventionmonteurs_id), INDEX IDX_B8B10ACF2B1455A5 (affairepiece_id), PRIMARY KEY(interventionmonteurs_id, affairepiece_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE interventionmonteurs_affairepiece ADD CONSTRAINT FK_B8B10ACF2B1455A5 FOREIGN KEY (affairepiece_id) REFERENCES affairepiece (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interventionmonteurs_affairepiece ADD CONSTRAINT FK_B8B10ACFACDF25C8 FOREIGN KEY (interventionmonteurs_id) REFERENCES interventionmonteurs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE AffairePiece ADD interventionMonteurs_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE AffairePiece ADD CONSTRAINT FK_50D0B71A2A43AA1F FOREIGN KEY (interventionMonteurs_id) REFERENCES interventionmonteurs (id)');
        $this->addSql('CREATE INDEX IDX_50D0B71A2A43AA1F ON AffairePiece (interventionMonteurs_id)');
    }
}
