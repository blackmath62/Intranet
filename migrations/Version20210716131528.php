<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210716131528 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE holiday ADD holidayType_id INT NOT NULL, ADD holidayStatus_id INT NOT NULL');
        $this->addSql('ALTER TABLE holiday ADD CONSTRAINT FK_13278BA8F6EBBFA0 FOREIGN KEY (holidayType_id) REFERENCES HolidayTypes (id)');
        $this->addSql('ALTER TABLE holiday ADD CONSTRAINT FK_13278BA8204C373C FOREIGN KEY (holidayStatus_id) REFERENCES statusHoliday (id)');
        $this->addSql('CREATE INDEX IDX_13278BA8F6EBBFA0 ON holiday (holidayType_id)');
        $this->addSql('CREATE INDEX IDX_13278BA8204C373C ON holiday (holidayStatus_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Holiday DROP FOREIGN KEY FK_13278BA8F6EBBFA0');
        $this->addSql('ALTER TABLE Holiday DROP FOREIGN KEY FK_13278BA8204C373C');
        $this->addSql('DROP INDEX IDX_13278BA8F6EBBFA0 ON Holiday');
        $this->addSql('DROP INDEX IDX_13278BA8204C373C ON Holiday');
        $this->addSql('ALTER TABLE Holiday DROP holidayType_id, DROP holidayStatus_id');
    }
}
