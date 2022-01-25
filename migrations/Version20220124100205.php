<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220124100205 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fsclistmovement ADD numCmd INT DEFAULT NULL, ADD dateCmd DATETIME DEFAULT NULL, ADD numBl INT DEFAULT NULL, ADD dateBl DATETIME DEFAULT NULL, ADD numFact INT DEFAULT NULL, ADD dateFact DATETIME DEFAULT NULL, CHANGE Utilisateur utilisateur VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fscListMovement DROP numCmd, DROP dateCmd, DROP numBl, DROP dateBl, DROP numFact, DROP dateFact, CHANGE utilisateur Utilisateur VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
