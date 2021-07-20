<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210716090352 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE decisionnel_copyfou DROP FOREIGN KEY FK_63A648A38F0EDD4F');
        $this->addSql('ALTER TABLE decisionnel_copyfou DROP FOREIGN KEY FK_63A648A3976348B8');
        $this->addSql('CREATE TABLE Holiday (id INT AUTO_INCREMENT NOT NULL, start DATETIME NOT NULL, end DATETIME NOT NULL, createdAt DATETIME NOT NULL, details LONGTEXT DEFAULT NULL, treatmentedAt DATETIME DEFAULT NULL, treatmentedBy_id INT DEFAULT NULL, INDEX IDX_13278BA842BB80A6 (treatmentedBy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE holiday_users (holiday_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_3D399A2E830A3EC0 (holiday_id), INDEX IDX_3D399A2E67B3B43D (users_id), PRIMARY KEY(holiday_id, users_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Holiday ADD CONSTRAINT FK_13278BA842BB80A6 FOREIGN KEY (treatmentedBy_id) REFERENCES Users (id)');
        $this->addSql('ALTER TABLE holiday_users ADD CONSTRAINT FK_3D399A2E830A3EC0 FOREIGN KEY (holiday_id) REFERENCES Holiday (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE holiday_users ADD CONSTRAINT FK_3D399A2E67B3B43D FOREIGN KEY (users_id) REFERENCES Users (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE copyart');
        $this->addSql('DROP TABLE copyfou');
        $this->addSql('DROP TABLE decisionnel');
        $this->addSql('DROP TABLE decisionnel_copyfou');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE holiday_users DROP FOREIGN KEY FK_3D399A2E830A3EC0');
        $this->addSql('CREATE TABLE copyart (id INT AUTO_INCREMENT NOT NULL, ref VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, des VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, dos INT NOT NULL, venun VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, closedAt DATETIME DEFAULT NULL, updatedAt DATETIME NOT NULL, metier VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE copyfou (id INT AUTO_INCREMENT NOT NULL, tiers VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, dos INT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, closedAt DATETIME DEFAULT NULL, updatedAt DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE decisionnel (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, INDEX IDX_BCB5B277A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE decisionnel_copyfou (decisionnel_id INT NOT NULL, copyfou_id INT NOT NULL, INDEX IDX_63A648A3976348B8 (decisionnel_id), INDEX IDX_63A648A38F0EDD4F (copyfou_id), PRIMARY KEY(decisionnel_id, copyfou_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE decisionnel ADD CONSTRAINT FK_BCB5B277A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE decisionnel_copyfou ADD CONSTRAINT FK_63A648A38F0EDD4F FOREIGN KEY (copyfou_id) REFERENCES copyfou (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE decisionnel_copyfou ADD CONSTRAINT FK_63A648A3976348B8 FOREIGN KEY (decisionnel_id) REFERENCES decisionnel (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE Holiday');
        $this->addSql('DROP TABLE holiday_users');
    }
}
