<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220719100126 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE holiday_users');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE holiday_users (holiday_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_3D399A2E830A3EC0 (holiday_id), INDEX IDX_3D399A2E67B3B43D (users_id), PRIMARY KEY(holiday_id, users_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE holiday_users ADD CONSTRAINT FK_3D399A2E67B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE holiday_users ADD CONSTRAINT FK_3D399A2E830A3EC0 FOREIGN KEY (holiday_id) REFERENCES holiday (id) ON DELETE CASCADE');
    }
}
