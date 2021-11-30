<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211126101903 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Note (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, content LONGTEXT NOT NULL, createdAt DATETIME NOT NULL, modifiedAt DATETIME DEFAULT NULL, INDEX IDX_6F8F552AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Note ADD CONSTRAINT FK_6F8F552AA76ED395 FOREIGN KEY (user_id) REFERENCES Users (id)');
        $this->addSql('DROP TABLE cmdrobydelaiacceptereporte_users');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cmdrobydelaiacceptereporte_users (cmdrobydelaiacceptereporte_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_534D2A28E7660094 (cmdrobydelaiacceptereporte_id), INDEX IDX_534D2A2867B3B43D (users_id), PRIMARY KEY(cmdrobydelaiacceptereporte_id, users_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE cmdrobydelaiacceptereporte_users ADD CONSTRAINT FK_534D2A2867B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cmdrobydelaiacceptereporte_users ADD CONSTRAINT FK_534D2A28E7660094 FOREIGN KEY (cmdrobydelaiacceptereporte_id) REFERENCES cmdrobydelaiacceptereporte (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE Note');
    }
}
