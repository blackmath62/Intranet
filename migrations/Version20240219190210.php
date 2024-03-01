<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240219190210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE MouvPreparationCmdAdminController (id INT AUTO_INCREMENT NOT NULL, assignedAt DATETIME DEFAULT NULL, preparedAt DATETIME DEFAULT NULL, cdNo INT NOT NULL, assignedBy_id INT DEFAULT NULL, preparedBy_id INT DEFAULT NULL, INDEX IDX_4DBA2FCDF7B85E2 (assignedBy_id), INDEX IDX_4DBA2FCE8B94F96 (preparedBy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE MouvPreparationCmdAdminController ADD CONSTRAINT FK_4DBA2FCDF7B85E2 FOREIGN KEY (assignedBy_id) REFERENCES Users (id)');
        $this->addSql('ALTER TABLE MouvPreparationCmdAdminController ADD CONSTRAINT FK_4DBA2FCE8B94F96 FOREIGN KEY (preparedBy_id) REFERENCES Users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE MouvPreparationCmdAdminController DROP FOREIGN KEY FK_4DBA2FCDF7B85E2');
        $this->addSql('ALTER TABLE MouvPreparationCmdAdminController DROP FOREIGN KEY FK_4DBA2FCE8B94F96');
        $this->addSql('DROP TABLE MouvPreparationCmdAdminController');
    }
}
