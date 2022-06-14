<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220428083514 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE movbillfsc_fsclistmovement (movbillfsc_id INT NOT NULL, fsclistmovement_id INT NOT NULL, INDEX IDX_FDDADF7C3436D52B (movbillfsc_id), INDEX IDX_FDDADF7CE520E0FE (fsclistmovement_id), PRIMARY KEY(movbillfsc_id, fsclistmovement_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE movbillfsc_fsclistmovement ADD CONSTRAINT FK_FDDADF7C3436D52B FOREIGN KEY (movbillfsc_id) REFERENCES MovBillFsc (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE movbillfsc_fsclistmovement ADD CONSTRAINT FK_FDDADF7CE520E0FE FOREIGN KEY (fsclistmovement_id) REFERENCES fscListMovement (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE movbillfsc_fsclistmovement');
    }
}
