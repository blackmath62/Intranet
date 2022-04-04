<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220401095224 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8E9018A42B36786B ON typedocumentfsc (title)');
        $this->addSql('ALTER TABLE documentsfsc ADD TypeDoc_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE documentsfsc ADD CONSTRAINT FK_62B1D0D545932531 FOREIGN KEY (TypeDoc_id) REFERENCES TypeDocumentFsc (id)');
        $this->addSql('CREATE INDEX IDX_62B1D0D545932531 ON documentsfsc (TypeDoc_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE documentsFsc DROP FOREIGN KEY FK_62B1D0D545932531');
        $this->addSql('DROP INDEX IDX_62B1D0D545932531 ON documentsFsc');
        $this->addSql('ALTER TABLE documentsFsc DROP TypeDoc_id');
        $this->addSql('DROP INDEX UNIQ_8E9018A42B36786B ON TypeDocumentFsc');
    }
}
