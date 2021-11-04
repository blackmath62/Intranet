<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211102144257 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Holiday (id INT AUTO_INCREMENT NOT NULL, createdAt DATETIME NOT NULL, details LONGTEXT DEFAULT NULL, treatmentedAt DATETIME DEFAULT NULL, sliceStart VARCHAR(255) NOT NULL, sliceEnd VARCHAR(255) NOT NULL, start DATETIME NOT NULL, end DATETIME NOT NULL, treatmentedBy_id INT DEFAULT NULL, holidayType_id INT NOT NULL, holidayStatus_id INT NOT NULL, INDEX IDX_13278BA842BB80A6 (treatmentedBy_id), INDEX IDX_13278BA8F6EBBFA0 (holidayType_id), INDEX IDX_13278BA8204C373C (holidayStatus_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE holiday_users (holiday_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_3D399A2E830A3EC0 (holiday_id), INDEX IDX_3D399A2E67B3B43D (users_id), PRIMARY KEY(holiday_id, users_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE HolidayTypes (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, createdAt DATETIME NOT NULL, color VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_BE1352AE86CA693C (createdAt), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Holiday ADD CONSTRAINT FK_13278BA842BB80A6 FOREIGN KEY (treatmentedBy_id) REFERENCES Users (id)');
        $this->addSql('ALTER TABLE Holiday ADD CONSTRAINT FK_13278BA8F6EBBFA0 FOREIGN KEY (holidayType_id) REFERENCES HolidayTypes (id)');
        $this->addSql('ALTER TABLE Holiday ADD CONSTRAINT FK_13278BA8204C373C FOREIGN KEY (holidayStatus_id) REFERENCES statusHoliday (id)');
        $this->addSql('ALTER TABLE holiday_users ADD CONSTRAINT FK_3D399A2E830A3EC0 FOREIGN KEY (holiday_id) REFERENCES Holiday (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE holiday_users ADD CONSTRAINT FK_3D399A2E67B3B43D FOREIGN KEY (users_id) REFERENCES Users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE holiday_users DROP FOREIGN KEY FK_3D399A2E830A3EC0');
        $this->addSql('ALTER TABLE Holiday DROP FOREIGN KEY FK_13278BA8F6EBBFA0');
        $this->addSql('DROP TABLE Holiday');
        $this->addSql('DROP TABLE holiday_users');
        $this->addSql('DROP TABLE HolidayTypes');
    }
}
