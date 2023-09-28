<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230926140832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alimentationemplacement DROP FOREIGN KEY FK_4B94DB363174800F');
        $this->addSql('ALTER TABLE annuaire DROP FOREIGN KEY FK_BC1DC55DFCF77503');
        $this->addSql('ALTER TABLE chats DROP FOREIGN KEY FK_ECA9370BA76ED395');
        $this->addSql('ALTER TABLE cmdrobydelaiacceptereporte DROP FOREIGN KEY FK_F1BB5812D6A05076');
        $this->addSql('ALTER TABLE commentaires DROP FOREIGN KEY FK_C18F1B3CA76ED395');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_A6E8F47C700047D2');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_A6E8F47CA76ED395');
        $this->addSql('ALTER TABLE controlearticlesfsc DROP FOREIGN KEY FK_824088FF96D13A0E');
        $this->addSql('ALTER TABLE documents DROP FOREIGN KEY FK_2041F02BA76ED395');
        $this->addSql('ALTER TABLE documents DROP FOREIGN KEY FK_2041F02BFCF77503');
        $this->addSql('ALTER TABLE documentsfsc DROP FOREIGN KEY FK_62B1D0D5EB7951B7');
        $this->addSql('ALTER TABLE documentsfsc DROP FOREIGN KEY FK_62B1D0D545932531');
        $this->addSql('ALTER TABLE documentsreglementairesfsc DROP FOREIGN KEY FK_E3BDB82738FBA2EB');
        $this->addSql('ALTER TABLE faq DROP FOREIGN KEY FK_7E583746A76ED395');
        $this->addSql('ALTER TABLE faq DROP FOREIGN KEY FK_7E583746CA84195D');
        $this->addSql('ALTER TABLE faq DROP FOREIGN KEY FK_7E583746650760A9');
        $this->addSql('ALTER TABLE fsclistmovement DROP FOREIGN KEY FK_546864BED1A4D98C');
        $this->addSql('ALTER TABLE holiday DROP FOREIGN KEY FK_13278BA8204C373C');
        $this->addSql('ALTER TABLE holiday DROP FOREIGN KEY FK_13278BA8F6EBBFA0');
        $this->addSql('ALTER TABLE holiday DROP FOREIGN KEY FK_13278BA842BB80A6');
        $this->addSql('ALTER TABLE holiday DROP FOREIGN KEY FK_13278BA8A76ED395');
        $this->addSql('ALTER TABLE interventionfichemonteur DROP FOREIGN KEY FK_A89440288EAE3863');
        $this->addSql('ALTER TABLE interventionfichemonteur DROP FOREIGN KEY FK_A8944028BA9CD190');
        $this->addSql('ALTER TABLE interventionfichemonteur DROP FOREIGN KEY FK_A89440281E253D71');
        $this->addSql('ALTER TABLE interventionfichemonteur DROP FOREIGN KEY FK_A8944028A9AD00D0');
        $this->addSql('ALTER TABLE interventionfichemonteur DROP FOREIGN KEY FK_A89440283174800F');
        $this->addSql('ALTER TABLE interventionfichemonteur DROP FOREIGN KEY FK_A8944028AB9A1716');
        $this->addSql('ALTER TABLE interventionfichesmonteursheures DROP FOREIGN KEY FK_8CF40281EBED9C32');
        $this->addSql('ALTER TABLE interventionfichesmonteursheures DROP FOREIGN KEY FK_8CF402813174800F');
        $this->addSql('ALTER TABLE interventionmonteurs DROP FOREIGN KEY FK_3B754F1727DAFE17');
        $this->addSql('ALTER TABLE interventionmonteurs DROP FOREIGN KEY FK_3B754F171E253D71');
        $this->addSql('ALTER TABLE interventionmonteurs DROP FOREIGN KEY FK_3B754F1724121FC0');
        $this->addSql('ALTER TABLE interventionmonteurs_affairepiece DROP FOREIGN KEY FK_B8B10ACF2B1455A5');
        $this->addSql('ALTER TABLE interventionmonteurs_affairepiece DROP FOREIGN KEY FK_B8B10ACFACDF25C8');
        $this->addSql('ALTER TABLE interventionmonteurs_users DROP FOREIGN KEY FK_6CA5267867B3B43D');
        $this->addSql('ALTER TABLE interventionmonteurs_users DROP FOREIGN KEY FK_6CA52678ACDF25C8');
        $this->addSql('ALTER TABLE listcmdtraite DROP FOREIGN KEY FK_70C87BA749F4847C');
        $this->addSql('ALTER TABLE movbillfsc DROP FOREIGN KEY FK_14C831943174800F');
        $this->addSql('ALTER TABLE movbillfsc_fsclistmovement DROP FOREIGN KEY FK_FDDADF7C3436D52B');
        $this->addSql('ALTER TABLE movbillfsc_fsclistmovement DROP FOREIGN KEY FK_FDDADF7CE520E0FE');
        $this->addSql('ALTER TABLE news DROP FOREIGN KEY FK_BDE1366EA76ED395');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_6F8F552A81D213CC');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_6F8F552AA76ED395');
        $this->addSql('ALTER TABLE othersdocuments DROP FOREIGN KEY FK_231AA45EA76ED395');
        $this->addSql('ALTER TABLE paysbanfsc DROP FOREIGN KEY FK_E69CA5DB29455BF7');
        $this->addSql('ALTER TABLE retraitmarchandisesean DROP FOREIGN KEY FK_3A0E9F693174800F');
        $this->addSql('ALTER TABLE signatureelectronique DROP FOREIGN KEY FK_F0FE9683174800F');
        $this->addSql('ALTER TABLE signatureelectronique DROP FOREIGN KEY FK_F0FE9688EAE3863');
        $this->addSql('ALTER TABLE tickets DROP FOREIGN KEY FK_9BFBA46855C16B5E');
        $this->addSql('ALTER TABLE tickets DROP FOREIGN KEY FK_9BFBA468ED5CA9E6');
        $this->addSql('ALTER TABLE tickets DROP FOREIGN KEY FK_9BFBA468A76ED395');
        $this->addSql('ALTER TABLE tickets DROP FOREIGN KEY FK_9BFBA468FCF77503');
        $this->addSql('ALTER TABLE tickets DROP FOREIGN KEY FK_9BFBA468497B19F9');
        $this->addSql('ALTER TABLE tickets DROP FOREIGN KEY FK_9BFBA468BE3DB2B7');
        $this->addSql('ALTER TABLE trackings DROP FOREIGN KEY FK_8D566D85A76ED395');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_D5428AEDED5CA9E6');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_D5428AEDFCF77503');
        $this->addSql('ALTER TABLE usersdivaltobyfunction DROP FOREIGN KEY FK_E74C0ACDD531E390');
        $this->addSql('DROP TABLE affairepiece');
        $this->addSql('DROP TABLE affaires');
        $this->addSql('DROP TABLE alimentationemplacement');
        $this->addSql('DROP TABLE annuaire');
        $this->addSql('DROP TABLE chats');
        $this->addSql('DROP TABLE cmdrobydelaiacceptereporte');
        $this->addSql('DROP TABLE commentaires');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE controlearticlesfsc');
        $this->addSql('DROP TABLE controlesanomalies');
        $this->addSql('DROP TABLE documents');
        $this->addSql('DROP TABLE documentsfsc');
        $this->addSql('DROP TABLE documentsreglementairesfsc');
        $this->addSql('DROP TABLE faq');
        $this->addSql('DROP TABLE fournisseursdivalto');
        $this->addSql('DROP TABLE fsclistmovement');
        $this->addSql('DROP TABLE holiday');
        $this->addSql('DROP TABLE holidaytypes');
        $this->addSql('DROP TABLE icd');
        $this->addSql('DROP TABLE interventionfichemonteur');
        $this->addSql('DROP TABLE interventionfichesmonteursheures');
        $this->addSql('DROP TABLE interventionmonteurs');
        $this->addSql('DROP TABLE interventionmonteurs_affairepiece');
        $this->addSql('DROP TABLE interventionmonteurs_users');
        $this->addSql('DROP TABLE listcmdtraite');
        $this->addSql('DROP TABLE listdivaltousers');
        $this->addSql('DROP TABLE logiciel');
        $this->addSql('DROP TABLE maillist');
        $this->addSql('DROP TABLE movbillfsc');
        $this->addSql('DROP TABLE movbillfsc_fsclistmovement');
        $this->addSql('DROP TABLE news');
        $this->addSql('DROP TABLE note');
        $this->addSql('DROP TABLE othersdocuments');
        $this->addSql('DROP TABLE paysbanfsc');
        $this->addSql('DROP TABLE prestataire');
        $this->addSql('DROP TABLE priorities');
        $this->addSql('DROP TABLE produitscommissionnaires');
        $this->addSql('DROP TABLE retraitmarchandisesean');
        $this->addSql('DROP TABLE sectionsearch');
        $this->addSql('DROP TABLE services');
        $this->addSql('DROP TABLE signatureelectronique');
        $this->addSql('DROP TABLE societe');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP TABLE statusholiday');
        $this->addSql('DROP TABLE tickets');
        $this->addSql('DROP TABLE trackings');
        $this->addSql('DROP TABLE typedocumentfsc');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE usersdivaltobyfunction');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE affairepiece (id INT AUTO_INCREMENT NOT NULL, entId INT NOT NULL, adresse VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, typePiece INT NOT NULL, piece INT NOT NULL, op VARCHAR(2) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, transport VARCHAR(5) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, etat VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, affaire VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, closedAt DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE affaires (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, libelle VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, tiers VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, progress INT DEFAULT NULL, start DATETIME DEFAULT NULL, end DATETIME DEFAULT NULL, textColor VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, backgroundColor VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, etat VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, duration VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE alimentationemplacement (id INT AUTO_INCREMENT NOT NULL, createdAt DATETIME NOT NULL, emplacement VARCHAR(8) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, sendAt DATETIME DEFAULT NULL, ean VARCHAR(13) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, createdBy_id INT DEFAULT NULL, INDEX IDX_4B94DB363174800F (createdBy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE annuaire (id INT AUTO_INCREMENT NOT NULL, societe_id INT NOT NULL, interne INT DEFAULT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, exterieur VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, mail VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, fonction VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, portable VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_BC1DC55DFCF77503 (societe_id), UNIQUE INDEX UNIQ_BC1DC55D6C6E55B5 (nom), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE chats (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, content LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, identifiant INT DEFAULT NULL, controller VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, fonction VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, tables VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_ECA9370BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE cmdrobydelaiacceptereporte (id INT AUTO_INCREMENT NOT NULL, identification VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, statut VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, modifiedAt DATETIME DEFAULT NULL, tiers VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, Nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, dateCmd DATETIME NOT NULL, notreRef VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, delaiAccepte DATETIME DEFAULT NULL, delaiReporte DATETIME DEFAULT NULL, modifiedBy_id INT DEFAULT NULL, cmd VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, tel VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ht VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_F1BB5812D6A05076 (modifiedBy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE commentaires (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, createdAt DATETIME NOT NULL, content LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, Tables VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, identifiant INT NOT NULL, INDEX IDX_C18F1B3CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, ticket_id INT NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, content LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, files VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_A6E8F47CA76ED395 (user_id), INDEX IDX_A6E8F47C700047D2 (ticket_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE controlearticlesfsc (id INT AUTO_INCREMENT NOT NULL, createdAt DATETIME NOT NULL, UpdatedAt DATETIME NOT NULL, products VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, status TINYINT(1) NOT NULL, controledBy_id INT DEFAULT NULL, LastOrder VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, LastOrderAt DATETIME NOT NULL, INDEX IDX_824088FF96D13A0E (controledBy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE controlesanomalies (id INT AUTO_INCREMENT NOT NULL, idAnomalie VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, modifiedAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, user VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE documents (id INT AUTO_INCREMENT NOT NULL, societe_id INT DEFAULT NULL, user_id INT NOT NULL, createdAt DATETIME NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, url VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, beginningDate DATETIME NOT NULL, endDate DATETIME NOT NULL, INDEX IDX_2041F02BA76ED395 (user_id), INDEX IDX_2041F02BFCF77503 (societe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE documentsfsc (id INT AUTO_INCREMENT NOT NULL, file VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, fscListMovement_id INT DEFAULT NULL, TypeDoc_id INT DEFAULT NULL, INDEX IDX_62B1D0D5EB7951B7 (fscListMovement_id), INDEX IDX_62B1D0D545932531 (TypeDoc_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE documentsreglementairesfsc (id INT AUTO_INCREMENT NOT NULL, createdAt DATETIME NOT NULL, files VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, years VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, addBy_id INT DEFAULT NULL, INDEX IDX_E3BDB82738FBA2EB (addBy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE faq (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, logiciel_id INT NOT NULL, search_id INT NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, content LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, INDEX IDX_7E583746CA84195D (logiciel_id), INDEX IDX_7E583746A76ED395 (user_id), INDEX IDX_7E583746650760A9 (search_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE fournisseursdivalto (id INT AUTO_INCREMENT NOT NULL, tiers VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE fsclistmovement (id INT AUTO_INCREMENT NOT NULL, utilisateur VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, updatedAt DATETIME DEFAULT NULL, numCmd INT DEFAULT NULL, dateCmd DATETIME DEFAULT NULL, numBl INT DEFAULT NULL, dateBl DATETIME DEFAULT NULL, numFact INT DEFAULT NULL, dateFact DATETIME DEFAULT NULL, createdAt DATETIME NOT NULL, tiers VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, codePiece INT NOT NULL, notreRef VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, status TINYINT(1) NOT NULL, Probleme TINYINT(1) NOT NULL, perimetreBois VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, updatePerimetreBoisFsc DATETIME DEFAULT NULL, userChangePerimetreBoisFsc_id INT DEFAULT NULL, INDEX IDX_546864BED1A4D98C (userChangePerimetreBoisFsc_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE holiday (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, start DATETIME NOT NULL, sliceStart VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, end DATETIME NOT NULL, sliceEnd VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, details LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, treatmentedAt DATETIME DEFAULT NULL, treatmentedBy_id INT DEFAULT NULL, holidayType_id INT NOT NULL, holidayStatus_id INT NOT NULL, nbJours NUMERIC(10, 1) NOT NULL, INDEX IDX_13278BA842BB80A6 (treatmentedBy_id), INDEX IDX_13278BA8204C373C (holidayStatus_id), INDEX IDX_13278BA8A76ED395 (user_id), INDEX IDX_13278BA8F6EBBFA0 (holidayType_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE holidaytypes (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, color VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_BE1352AE86CA693C (createdAt), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE icd (id INT AUTO_INCREMENT NOT NULL, ref VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, sref1 VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, sref2 VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, designation VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, qte VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, pu VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, pu_corrige VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, commentaires VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE interventionfichemonteur (id INT AUTO_INCREMENT NOT NULL, intervenant_id INT DEFAULT NULL, commentaire_id INT DEFAULT NULL, intervention_id INT DEFAULT NULL, createdAt DATETIME NOT NULL, pension LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\', createdBy_id INT DEFAULT NULL, validedAt DATETIME DEFAULT NULL, validedBy_id INT DEFAULT NULL, lockedAt DATETIME DEFAULT NULL, lockedBy_id INT DEFAULT NULL, comment VARCHAR(1000) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, here TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_A8944028BA9CD190 (commentaire_id), INDEX IDX_A8944028AB9A1716 (intervenant_id), INDEX IDX_A8944028A9AD00D0 (validedBy_id), INDEX IDX_A89440283174800F (createdBy_id), INDEX IDX_A89440288EAE3863 (intervention_id), INDEX IDX_A89440281E253D71 (lockedBy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE interventionfichesmonteursheures (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, start TIME NOT NULL, end TIME NOT NULL, createdAt DATETIME NOT NULL, createdBy_id INT DEFAULT NULL, interventionFicheMonteur_id INT DEFAULT NULL, INDEX IDX_8CF40281EBED9C32 (interventionFicheMonteur_id), INDEX IDX_8CF402813174800F (createdBy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE interventionmonteurs (id INT AUTO_INCREMENT NOT NULL, code_id INT DEFAULT NULL, start DATETIME NOT NULL, end DATETIME NOT NULL, adresse VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, UserCr_id INT DEFAULT NULL, backgroundColor VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, textColor VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME DEFAULT NULL, lockedAt DATETIME DEFAULT NULL, lockedBy_id INT DEFAULT NULL, sendAt DATETIME DEFAULT NULL, INDEX IDX_3B754F1724121FC0 (UserCr_id), INDEX IDX_3B754F171E253D71 (lockedBy_id), INDEX IDX_3B754F1727DAFE17 (code_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE interventionmonteurs_affairepiece (interventionmonteurs_id INT NOT NULL, affairepiece_id INT NOT NULL, INDEX IDX_B8B10ACF2B1455A5 (affairepiece_id), INDEX IDX_B8B10ACFACDF25C8 (interventionmonteurs_id), PRIMARY KEY(interventionmonteurs_id, affairepiece_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE interventionmonteurs_users (users_id INT NOT NULL, interventionmonteurs_id INT NOT NULL, INDEX IDX_6CA52678ACDF25C8 (interventionmonteurs_id), INDEX IDX_6CA5267867B3B43D (users_id), PRIMARY KEY(interventionmonteurs_id, users_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE listcmdtraite (id INT AUTO_INCREMENT NOT NULL, numero VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, dossier VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, treatedBy_id INT DEFAULT NULL, INDEX IDX_70C87BA749F4847C (treatedBy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE listdivaltousers (id INT AUTO_INCREMENT NOT NULL, divalto_id INT NOT NULL, userX VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, dos VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, valid TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE logiciel (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, textColor VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, backgroungColor VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, icon VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, closedAt DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE maillist (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, page VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, SecondOption VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE movbillfsc (id INT AUTO_INCREMENT NOT NULL, createdAt DATETIME NOT NULL, facture INT NOT NULL, dateFact DATETIME NOT NULL, tiers VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, notreRef VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, TypeTiers VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, createdBy_id INT DEFAULT NULL, dateBl DATETIME DEFAULT NULL, bl INT DEFAULT NULL, anomalie TINYINT(1) DEFAULT NULL, INDEX IDX_14C831943174800F (createdBy_id), UNIQUE INDEX UNIQ_14C83194FE866410 (facture), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE movbillfsc_fsclistmovement (movbillfsc_id INT NOT NULL, fsclistmovement_id INT NOT NULL, INDEX IDX_FDDADF7CE520E0FE (fsclistmovement_id), INDEX IDX_FDDADF7C3436D52B (movbillfsc_id), PRIMARY KEY(movbillfsc_id, fsclistmovement_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE news (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, createdAt DATETIME NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, content LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_BDE1366EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE note (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, content LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, modifiedAt DATETIME DEFAULT NULL, cmdRobyDelaiAccepteReporte_id INT DEFAULT NULL, INDEX IDX_6F8F552AA76ED395 (user_id), INDEX IDX_6F8F552A81D213CC (cmdRobyDelaiAccepteReporte_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE othersdocuments (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, createdAt DATETIME NOT NULL, file VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, tables VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, identifiant INT NOT NULL, Parametre VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_231AA45EA76ED395 (user_id), UNIQUE INDEX UNIQ_231AA45E8C9F3610 (file), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE paysbanfsc (id INT AUTO_INCREMENT NOT NULL, pays VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, CreatedBy_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_E69CA5DB349F3CAE (pays), INDEX IDX_E69CA5DB29455BF7 (CreatedBy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE prestataire (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, affiliation VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, phone VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, img VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, color VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE priorities (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, color VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, textColor VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, closedAt DATETIME NOT NULL, fa VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE produitscommissionnaires (id INT AUTO_INCREMENT NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, reference VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, designation VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, contratCommissionaire TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE retraitmarchandisesean (id INT AUTO_INCREMENT NOT NULL, createdAt DATETIME NOT NULL, chantier VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, sendAt DATETIME DEFAULT NULL, createdBy_id INT DEFAULT NULL, ean VARCHAR(13) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, qte DOUBLE PRECISION NOT NULL, stockFaux TINYINT(1) DEFAULT NULL, INDEX IDX_3A0E9F693174800F (createdBy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE sectionsearch (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, closedAt DATETIME DEFAULT NULL, textColor VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, backgroundColor VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, fa VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE services (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, color VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, closedAt DATETIME DEFAULT NULL, textColor VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, fa VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE signatureelectronique (id INT AUTO_INCREMENT NOT NULL, intervention_id INT DEFAULT NULL, signatureId VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, documentId VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, signerId VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, pdfSansSignature VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, createdBy_id INT DEFAULT NULL, INDEX IDX_F0FE9688EAE3863 (intervention_id), INDEX IDX_F0FE9683174800F (createdBy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE societe (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, closedAt DATETIME DEFAULT NULL, dossier VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, img VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, parameter VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_D6D804216C6E55B5 (nom), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE status (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, ClosedAt DATETIME DEFAULT NULL, backgroundColor VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, textColor VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, fa VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE statusholiday (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE tickets (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, statu_id INT NOT NULL, societe_id INT DEFAULT NULL, user_id INT NOT NULL, priority_id INT NOT NULL, prestataire_id INT DEFAULT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, content LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, closedAt DATETIME DEFAULT NULL, file VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, modifiedAt DATETIME DEFAULT NULL, INDEX IDX_9BFBA468BE3DB2B7 (prestataire_id), INDEX IDX_9BFBA46855C16B5E (statu_id), INDEX IDX_9BFBA468A76ED395 (user_id), INDEX IDX_9BFBA468497B19F9 (priority_id), INDEX IDX_9BFBA468ED5CA9E6 (service_id), INDEX IDX_9BFBA468FCF77503 (societe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE trackings (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, page VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, INDEX IDX_8D566D85A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE typedocumentfsc (id INT AUTO_INCREMENT NOT NULL, createdAt DATETIME NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, color VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, icone VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_8E9018A42B36786B (title), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, societe_id INT NOT NULL, service_id INT DEFAULT NULL, email VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, pseudo VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, img VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, roles JSON NOT NULL, bornAt DATETIME DEFAULT NULL, commercial VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, closedAt DATETIME DEFAULT NULL, ev TINYINT(1) DEFAULT NULL, hp TINYINT(1) DEFAULT NULL, me TINYINT(1) DEFAULT NULL, interne VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, exterieur VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, fonction VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, portable VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_D5428AEDE7927C74 (email), UNIQUE INDEX UNIQ_D5428AED7653F3AE (commercial), INDEX IDX_D5428AEDED5CA9E6 (service_id), UNIQUE INDEX UNIQ_D5428AED86CC499D (pseudo), INDEX IDX_D5428AEDFCF77503 (societe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE usersdivaltobyfunction (id INT AUTO_INCREMENT NOT NULL, functions VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, divaltoId_id INT DEFAULT NULL, INDEX IDX_E74C0ACDD531E390 (divaltoId_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE alimentationemplacement ADD CONSTRAINT FK_4B94DB363174800F FOREIGN KEY (createdBy_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE annuaire ADD CONSTRAINT FK_BC1DC55DFCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id)');
        $this->addSql('ALTER TABLE chats ADD CONSTRAINT FK_ECA9370BA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE cmdrobydelaiacceptereporte ADD CONSTRAINT FK_F1BB5812D6A05076 FOREIGN KEY (modifiedBy_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE commentaires ADD CONSTRAINT FK_C18F1B3CA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_A6E8F47C700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_A6E8F47CA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE controlearticlesfsc ADD CONSTRAINT FK_824088FF96D13A0E FOREIGN KEY (controledBy_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE documents ADD CONSTRAINT FK_2041F02BA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE documents ADD CONSTRAINT FK_2041F02BFCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id)');
        $this->addSql('ALTER TABLE documentsfsc ADD CONSTRAINT FK_62B1D0D5EB7951B7 FOREIGN KEY (fscListMovement_id) REFERENCES fsclistmovement (id)');
        $this->addSql('ALTER TABLE documentsfsc ADD CONSTRAINT FK_62B1D0D545932531 FOREIGN KEY (TypeDoc_id) REFERENCES typedocumentfsc (id)');
        $this->addSql('ALTER TABLE documentsreglementairesfsc ADD CONSTRAINT FK_E3BDB82738FBA2EB FOREIGN KEY (addBy_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE faq ADD CONSTRAINT FK_7E583746A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE faq ADD CONSTRAINT FK_7E583746CA84195D FOREIGN KEY (logiciel_id) REFERENCES logiciel (id)');
        $this->addSql('ALTER TABLE faq ADD CONSTRAINT FK_7E583746650760A9 FOREIGN KEY (search_id) REFERENCES sectionsearch (id)');
        $this->addSql('ALTER TABLE fsclistmovement ADD CONSTRAINT FK_546864BED1A4D98C FOREIGN KEY (userChangePerimetreBoisFsc_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE holiday ADD CONSTRAINT FK_13278BA8204C373C FOREIGN KEY (holidayStatus_id) REFERENCES statusholiday (id)');
        $this->addSql('ALTER TABLE holiday ADD CONSTRAINT FK_13278BA8F6EBBFA0 FOREIGN KEY (holidayType_id) REFERENCES holidaytypes (id)');
        $this->addSql('ALTER TABLE holiday ADD CONSTRAINT FK_13278BA842BB80A6 FOREIGN KEY (treatmentedBy_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE holiday ADD CONSTRAINT FK_13278BA8A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE interventionfichemonteur ADD CONSTRAINT FK_A89440288EAE3863 FOREIGN KEY (intervention_id) REFERENCES interventionmonteurs (id)');
        $this->addSql('ALTER TABLE interventionfichemonteur ADD CONSTRAINT FK_A8944028BA9CD190 FOREIGN KEY (commentaire_id) REFERENCES commentaires (id)');
        $this->addSql('ALTER TABLE interventionfichemonteur ADD CONSTRAINT FK_A89440281E253D71 FOREIGN KEY (lockedBy_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE interventionfichemonteur ADD CONSTRAINT FK_A8944028A9AD00D0 FOREIGN KEY (validedBy_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE interventionfichemonteur ADD CONSTRAINT FK_A89440283174800F FOREIGN KEY (createdBy_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE interventionfichemonteur ADD CONSTRAINT FK_A8944028AB9A1716 FOREIGN KEY (intervenant_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE interventionfichesmonteursheures ADD CONSTRAINT FK_8CF40281EBED9C32 FOREIGN KEY (interventionFicheMonteur_id) REFERENCES interventionfichemonteur (id)');
        $this->addSql('ALTER TABLE interventionfichesmonteursheures ADD CONSTRAINT FK_8CF402813174800F FOREIGN KEY (createdBy_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE interventionmonteurs ADD CONSTRAINT FK_3B754F1727DAFE17 FOREIGN KEY (code_id) REFERENCES affaires (id)');
        $this->addSql('ALTER TABLE interventionmonteurs ADD CONSTRAINT FK_3B754F171E253D71 FOREIGN KEY (lockedBy_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE interventionmonteurs ADD CONSTRAINT FK_3B754F1724121FC0 FOREIGN KEY (UserCr_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE interventionmonteurs_affairepiece ADD CONSTRAINT FK_B8B10ACF2B1455A5 FOREIGN KEY (affairepiece_id) REFERENCES affairepiece (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interventionmonteurs_affairepiece ADD CONSTRAINT FK_B8B10ACFACDF25C8 FOREIGN KEY (interventionmonteurs_id) REFERENCES interventionmonteurs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interventionmonteurs_users ADD CONSTRAINT FK_6CA5267867B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interventionmonteurs_users ADD CONSTRAINT FK_6CA52678ACDF25C8 FOREIGN KEY (interventionmonteurs_id) REFERENCES interventionmonteurs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE listcmdtraite ADD CONSTRAINT FK_70C87BA749F4847C FOREIGN KEY (treatedBy_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE movbillfsc ADD CONSTRAINT FK_14C831943174800F FOREIGN KEY (createdBy_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE movbillfsc_fsclistmovement ADD CONSTRAINT FK_FDDADF7C3436D52B FOREIGN KEY (movbillfsc_id) REFERENCES movbillfsc (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE movbillfsc_fsclistmovement ADD CONSTRAINT FK_FDDADF7CE520E0FE FOREIGN KEY (fsclistmovement_id) REFERENCES fsclistmovement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE news ADD CONSTRAINT FK_BDE1366EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_6F8F552A81D213CC FOREIGN KEY (cmdRobyDelaiAccepteReporte_id) REFERENCES cmdrobydelaiacceptereporte (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_6F8F552AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE othersdocuments ADD CONSTRAINT FK_231AA45EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE paysbanfsc ADD CONSTRAINT FK_E69CA5DB29455BF7 FOREIGN KEY (CreatedBy_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE retraitmarchandisesean ADD CONSTRAINT FK_3A0E9F693174800F FOREIGN KEY (createdBy_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE signatureelectronique ADD CONSTRAINT FK_F0FE9683174800F FOREIGN KEY (createdBy_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE signatureelectronique ADD CONSTRAINT FK_F0FE9688EAE3863 FOREIGN KEY (intervention_id) REFERENCES interventionmonteurs (id)');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT FK_9BFBA46855C16B5E FOREIGN KEY (statu_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT FK_9BFBA468ED5CA9E6 FOREIGN KEY (service_id) REFERENCES services (id)');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT FK_9BFBA468A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT FK_9BFBA468FCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id)');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT FK_9BFBA468497B19F9 FOREIGN KEY (priority_id) REFERENCES priorities (id)');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT FK_9BFBA468BE3DB2B7 FOREIGN KEY (prestataire_id) REFERENCES prestataire (id)');
        $this->addSql('ALTER TABLE trackings ADD CONSTRAINT FK_8D566D85A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_D5428AEDED5CA9E6 FOREIGN KEY (service_id) REFERENCES services (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_D5428AEDFCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id)');
        $this->addSql('ALTER TABLE usersdivaltobyfunction ADD CONSTRAINT FK_E74C0ACDD531E390 FOREIGN KEY (divaltoId_id) REFERENCES listdivaltousers (id)');
    }
}
