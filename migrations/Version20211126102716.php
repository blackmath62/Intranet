<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211126102716 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE note ADD cmdRobyDelaiAccepteReporte_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_6F8F552A81D213CC FOREIGN KEY (cmdRobyDelaiAccepteReporte_id) REFERENCES CmdRobyDelaiAccepteReporte (id)');
        $this->addSql('CREATE INDEX IDX_6F8F552A81D213CC ON note (cmdRobyDelaiAccepteReporte_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Note DROP FOREIGN KEY FK_6F8F552A81D213CC');
        $this->addSql('DROP INDEX IDX_6F8F552A81D213CC ON Note');
        $this->addSql('ALTER TABLE Note DROP cmdRobyDelaiAccepteReporte_id');
    }
}
