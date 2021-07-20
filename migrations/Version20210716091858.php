<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210716091858 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users ADD service_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_D5428AEDED5CA9E6 FOREIGN KEY (service_id) REFERENCES Services (id)');
        $this->addSql('CREATE INDEX IDX_D5428AEDED5CA9E6 ON users (service_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Users DROP FOREIGN KEY FK_D5428AEDED5CA9E6');
        $this->addSql('DROP INDEX IDX_D5428AEDED5CA9E6 ON Users');
        $this->addSql('ALTER TABLE Users DROP service_id');
    }
}
