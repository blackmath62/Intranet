<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230912101849 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE interventionmonteurs_users (users_id INT NOT NULL, interventionmonteurs_id INT NOT NULL, INDEX IDX_6CA5267867B3B43D (users_id), INDEX IDX_6CA52678ACDF25C8 (interventionmonteurs_id), PRIMARY KEY(users_id, interventionmonteurs_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE interventionmonteurs_users ADD CONSTRAINT FK_6CA5267867B3B43D FOREIGN KEY (users_id) REFERENCES Users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interventionmonteurs_users ADD CONSTRAINT FK_6CA52678ACDF25C8 FOREIGN KEY (interventionmonteurs_id) REFERENCES InterventionMonteurs (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE users_interventionmonteurs');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE users_interventionmonteurs (users_id INT NOT NULL, interventionmonteurs_id INT NOT NULL, INDEX IDX_C84799C267B3B43D (users_id), INDEX IDX_C84799C2ACDF25C8 (interventionmonteurs_id), PRIMARY KEY(users_id, interventionmonteurs_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE users_interventionmonteurs ADD CONSTRAINT FK_C84799C267B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users_interventionmonteurs ADD CONSTRAINT FK_C84799C2ACDF25C8 FOREIGN KEY (interventionmonteurs_id) REFERENCES interventionmonteurs (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE interventionmonteurs_users');
    }
}
