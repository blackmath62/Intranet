<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210927155038 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentnews_users DROP FOREIGN KEY FK_CADE62FC8684E631');
        $this->addSql('DROP TABLE commentnews');
        $this->addSql('DROP TABLE commentnews_users');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commentnews (id INT AUTO_INCREMENT NOT NULL, message LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE commentnews_users (commentnews_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_CADE62FC8684E631 (commentnews_id), INDEX IDX_CADE62FC67B3B43D (users_id), PRIMARY KEY(commentnews_id, users_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE commentnews_users ADD CONSTRAINT FK_CADE62FC67B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commentnews_users ADD CONSTRAINT FK_CADE62FC8684E631 FOREIGN KEY (commentnews_id) REFERENCES commentnews (id) ON DELETE CASCADE');
    }
}
