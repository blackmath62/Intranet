<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230629154225 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE permissions');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE permissions (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, feature_id INT NOT NULL, INDEX IDX_AB7143B860E4B879 (feature_id), INDEX IDX_AB7143B8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE permissions ADD CONSTRAINT FK_AB7143B860E4B879 FOREIGN KEY (feature_id) REFERENCES features (id)');
        $this->addSql('ALTER TABLE permissions ADD CONSTRAINT FK_AB7143B8A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }
}
