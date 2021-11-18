<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211117103419 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE listcmdtraite ADD treatedBy_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE listcmdtraite ADD CONSTRAINT FK_70C87BA749F4847C FOREIGN KEY (treatedBy_id) REFERENCES Users (id)');
        $this->addSql('CREATE INDEX IDX_70C87BA749F4847C ON listcmdtraite (treatedBy_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ListCmdTraite DROP FOREIGN KEY FK_70C87BA749F4847C');
        $this->addSql('DROP INDEX IDX_70C87BA749F4847C ON ListCmdTraite');
        $this->addSql('ALTER TABLE ListCmdTraite DROP treatedBy_id');
    }
}
