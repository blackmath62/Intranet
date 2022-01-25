<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220124142036 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE documentsfsc ADD fscListMovement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE documentsfsc ADD CONSTRAINT FK_62B1D0D5EB7951B7 FOREIGN KEY (fscListMovement_id) REFERENCES fscListMovement (id)');
        $this->addSql('CREATE INDEX IDX_62B1D0D5EB7951B7 ON documentsfsc (fscListMovement_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE documentsFsc DROP FOREIGN KEY FK_62B1D0D5EB7951B7');
        $this->addSql('DROP INDEX IDX_62B1D0D5EB7951B7 ON documentsFsc');
        $this->addSql('ALTER TABLE documentsFsc DROP fscListMovement_id');
    }
}
