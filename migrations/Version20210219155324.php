<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210219155324 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ideabox ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE ideabox ADD CONSTRAINT FK_C2771D0BA76ED395 FOREIGN KEY (user_id) REFERENCES Users (id)');
        $this->addSql('CREATE INDEX IDX_C2771D0BA76ED395 ON ideabox (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE IdeaBox DROP FOREIGN KEY FK_C2771D0BA76ED395');
        $this->addSql('DROP INDEX IDX_C2771D0BA76ED395 ON IdeaBox');
        $this->addSql('ALTER TABLE IdeaBox DROP user_id');
    }
}
