<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201127161258 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annuaire RENAME INDEX uniq_456ba70b6c6e55b5 TO UNIQ_BC1DC55D6C6E55B5');
        $this->addSql('ALTER TABLE annuaire RENAME INDEX idx_456ba70bfcf77503 TO IDX_BC1DC55DFCF77503');
        $this->addSql('ALTER TABLE chats CHANGE created_at createdAt DATETIME NOT NULL');
        $this->addSql('ALTER TABLE chats RENAME INDEX idx_2d68180fa76ed395 TO IDX_ECA9370BA76ED395');
        $this->addSql('ALTER TABLE comments CHANGE created_at createdAt DATETIME NOT NULL');
        $this->addSql('ALTER TABLE comments RENAME INDEX idx_5f9e962aa76ed395 TO IDX_A6E8F47CA76ED395');
        $this->addSql('ALTER TABLE comments RENAME INDEX idx_5f9e962a700047d2 TO IDX_A6E8F47C700047D2');
        $this->addSql('ALTER TABLE documents ADD createdAt DATETIME NOT NULL, ADD beginningDate DATETIME NOT NULL, ADD endDate DATETIME NOT NULL, DROP created_at, DROP beginning_date, DROP end_date');
        $this->addSql('ALTER TABLE documents RENAME INDEX idx_a2b07288fcf77503 TO IDX_2041F02BFCF77503');
        $this->addSql('ALTER TABLE documents RENAME INDEX idx_a2b07288a76ed395 TO IDX_2041F02BA76ED395');
        $this->addSql('ALTER TABLE features CHANGE created_at createdAt DATETIME NOT NULL');
        $this->addSql('ALTER TABLE permissions RENAME INDEX idx_2dedcc6fa76ed395 TO IDX_AB7143B8A76ED395');
        $this->addSql('ALTER TABLE permissions RENAME INDEX idx_2dedcc6f60e4b879 TO IDX_AB7143B860E4B879');
        $this->addSql('ALTER TABLE societe CHANGE created_at createdAt DATETIME NOT NULL, CHANGE closed_at closedAt DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE societe RENAME INDEX uniq_19653dbd6c6e55b5 TO UNIQ_D6D804216C6E55B5');
        $this->addSql('ALTER TABLE tickets CHANGE created_at createdAt DATETIME NOT NULL');
        $this->addSql('ALTER TABLE tickets RENAME INDEX idx_54469df4ed5ca9e6 TO IDX_9BFBA468ED5CA9E6');
        $this->addSql('ALTER TABLE tickets RENAME INDEX idx_54469df455c16b5e TO IDX_9BFBA46855C16B5E');
        $this->addSql('ALTER TABLE tickets RENAME INDEX idx_54469df4fcf77503 TO IDX_9BFBA468FCF77503');
        $this->addSql('ALTER TABLE tickets RENAME INDEX idx_54469df4a76ed395 TO IDX_9BFBA468A76ED395');
        $this->addSql('ALTER TABLE tickets RENAME INDEX idx_54469df4497b19f9 TO IDX_9BFBA468497B19F9');
        $this->addSql('ALTER TABLE users CHANGE created_at createdAt DATETIME NOT NULL, CHANGE born_at bornAt DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE users RENAME INDEX uniq_1483a5e9e7927c74 TO UNIQ_D5428AEDE7927C74');
        $this->addSql('ALTER TABLE users RENAME INDEX uniq_1483a5e986cc499d TO UNIQ_D5428AED86CC499D');
        $this->addSql('ALTER TABLE users RENAME INDEX idx_1483a5e9fcf77503 TO IDX_D5428AEDFCF77503');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Annuaire RENAME INDEX idx_bc1dc55dfcf77503 TO IDX_456BA70BFCF77503');
        $this->addSql('ALTER TABLE Annuaire RENAME INDEX uniq_bc1dc55d6c6e55b5 TO UNIQ_456BA70B6C6E55B5');
        $this->addSql('ALTER TABLE Chats CHANGE createdat created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE Chats RENAME INDEX idx_eca9370ba76ed395 TO IDX_2D68180FA76ED395');
        $this->addSql('ALTER TABLE Comments CHANGE createdat created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE Comments RENAME INDEX idx_a6e8f47c700047d2 TO IDX_5F9E962A700047D2');
        $this->addSql('ALTER TABLE Comments RENAME INDEX idx_a6e8f47ca76ed395 TO IDX_5F9E962AA76ED395');
        $this->addSql('ALTER TABLE Documents ADD created_at DATETIME NOT NULL, ADD beginning_date DATETIME NOT NULL, ADD end_date DATETIME NOT NULL, DROP createdAt, DROP beginningDate, DROP endDate');
        $this->addSql('ALTER TABLE Documents RENAME INDEX idx_2041f02ba76ed395 TO IDX_A2B07288A76ED395');
        $this->addSql('ALTER TABLE Documents RENAME INDEX idx_2041f02bfcf77503 TO IDX_A2B07288FCF77503');
        $this->addSql('ALTER TABLE Features CHANGE createdat created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE Permissions RENAME INDEX idx_ab7143b860e4b879 TO IDX_2DEDCC6F60E4B879');
        $this->addSql('ALTER TABLE Permissions RENAME INDEX idx_ab7143b8a76ed395 TO IDX_2DEDCC6FA76ED395');
        $this->addSql('ALTER TABLE Societe CHANGE createdat created_at DATETIME NOT NULL, CHANGE closedat closed_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE Societe RENAME INDEX uniq_d6d804216c6e55b5 TO UNIQ_19653DBD6C6E55B5');
        $this->addSql('ALTER TABLE Tickets CHANGE createdat created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE Tickets RENAME INDEX idx_9bfba46855c16b5e TO IDX_54469DF455C16B5E');
        $this->addSql('ALTER TABLE Tickets RENAME INDEX idx_9bfba468a76ed395 TO IDX_54469DF4A76ED395');
        $this->addSql('ALTER TABLE Tickets RENAME INDEX idx_9bfba468ed5ca9e6 TO IDX_54469DF4ED5CA9E6');
        $this->addSql('ALTER TABLE Tickets RENAME INDEX idx_9bfba468fcf77503 TO IDX_54469DF4FCF77503');
        $this->addSql('ALTER TABLE Tickets RENAME INDEX idx_9bfba468497b19f9 TO IDX_54469DF4497B19F9');
        $this->addSql('ALTER TABLE Users CHANGE createdat created_at DATETIME NOT NULL, CHANGE bornat born_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE Users RENAME INDEX uniq_d5428aed86cc499d TO UNIQ_1483A5E986CC499D');
        $this->addSql('ALTER TABLE Users RENAME INDEX uniq_d5428aede7927c74 TO UNIQ_1483A5E9E7927C74');
        $this->addSql('ALTER TABLE Users RENAME INDEX idx_d5428aedfcf77503 TO IDX_1483A5E9FCF77503');
    }
}
