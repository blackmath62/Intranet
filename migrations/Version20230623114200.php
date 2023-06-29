<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230623114200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE affaires DROP adresse');
        $this->addSql('ALTER TABLE users ADD interne VARCHAR(255) DEFAULT NULL, ADD exterieur VARCHAR(255) DEFAULT NULL, ADD fonction VARCHAR(255) DEFAULT NULL, ADD portable VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Affaires ADD adresse VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE Users DROP interne, DROP exterieur, DROP fonction, DROP portable');
    }
}
