<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240110121739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE interventionmonteurs_affairepiece DROP FOREIGN KEY FK_B8B10ACF2B1455A5');
        $this->addSql('ALTER TABLE interventionmonteurs_affairepiece DROP FOREIGN KEY FK_B8B10ACFACDF25C8');
        $this->addSql('DROP TABLE interventionmonteurs_affairepiece');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE interventionmonteurs_affairepiece (interventionmonteurs_id INT NOT NULL, affairepiece_id INT NOT NULL, INDEX IDX_B8B10ACF2B1455A5 (affairepiece_id), INDEX IDX_B8B10ACFACDF25C8 (interventionmonteurs_id), PRIMARY KEY(interventionmonteurs_id, affairepiece_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE interventionmonteurs_affairepiece ADD CONSTRAINT FK_B8B10ACF2B1455A5 FOREIGN KEY (affairepiece_id) REFERENCES affairepiece (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interventionmonteurs_affairepiece ADD CONSTRAINT FK_B8B10ACFACDF25C8 FOREIGN KEY (interventionmonteurs_id) REFERENCES interventionmonteurs (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
