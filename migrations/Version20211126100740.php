<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211126100740 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cmdrobydelaiacceptereporte_users (cmdrobydelaiacceptereporte_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_534D2A28E7660094 (cmdrobydelaiacceptereporte_id), INDEX IDX_534D2A2867B3B43D (users_id), PRIMARY KEY(cmdrobydelaiacceptereporte_id, users_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cmdrobydelaiacceptereporte_users ADD CONSTRAINT FK_534D2A28E7660094 FOREIGN KEY (cmdrobydelaiacceptereporte_id) REFERENCES CmdRobyDelaiAccepteReporte (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cmdrobydelaiacceptereporte_users ADD CONSTRAINT FK_534D2A2867B3B43D FOREIGN KEY (users_id) REFERENCES Users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE cmdrobydelaiacceptereporte_users');
    }
}
