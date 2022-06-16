<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220616152245 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fsclistmovement ADD updatePerimetreBoisFsc DATETIME DEFAULT NULL, ADD userChangePerimetreBoisFsc_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fsclistmovement ADD CONSTRAINT FK_546864BED1A4D98C FOREIGN KEY (userChangePerimetreBoisFsc_id) REFERENCES Users (id)');
        $this->addSql('CREATE INDEX IDX_546864BED1A4D98C ON fsclistmovement (userChangePerimetreBoisFsc_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fscListMovement DROP FOREIGN KEY FK_546864BED1A4D98C');
        $this->addSql('DROP INDEX IDX_546864BED1A4D98C ON fscListMovement');
        $this->addSql('ALTER TABLE fscListMovement DROP updatePerimetreBoisFsc, DROP userChangePerimetreBoisFsc_id');
    }
}
