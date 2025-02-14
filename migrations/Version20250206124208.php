<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250206124208 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, galerie_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, prix DOUBLE PRECISION NOT NULL, date_pub DATE NOT NULL, disponible TINYINT(1) NOT NULL, nbrarticle INT NOT NULL, nbrlikes INT NOT NULL, contenu VARCHAR(255) DEFAULT NULL, categorie VARCHAR(255) DEFAULT NULL, INDEX IDX_23A0E66825396CB (galerie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE candidature (id INT AUTO_INCREMENT NOT NULL, candidat INT DEFAULT NULL, competences LONGTEXT DEFAULT NULL, disponibilite VARCHAR(20) NOT NULL, tarif_horaire NUMERIC(10, 2) DEFAULT NULL, date_creation DATETIME NOT NULL, INDEX IDX_E33BD3B86AB5B471 (candidat), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE candidature_offre (id INT AUTO_INCREMENT NOT NULL, candidature_id INT DEFAULT NULL, emploi_id INT DEFAULT NULL, statut VARCHAR(20) NOT NULL, date_association DATETIME NOT NULL, INDEX IDX_91FCEF3BB6121583 (candidature_id), INDEX IDX_91FCEF3BEC013E12 (emploi_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie (categorie_id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, date_creation DATE NOT NULL, PRIMARY KEY(categorie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE donation (iddon INT AUTO_INCREMENT NOT NULL, idevent INT DEFAULT NULL, userid INT DEFAULT NULL, donorname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, montant DOUBLE PRECISION NOT NULL, date DATE NOT NULL, payment_method VARCHAR(255) NOT NULL, num_tlf VARCHAR(20) DEFAULT NULL, INDEX IDX_31E581A0EDAB66BE (idevent), INDEX IDX_31E581A0F132696E (userid), PRIMARY KEY(iddon)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE emploi (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, competences_requises LONGTEXT DEFAULT NULL, budget NUMERIC(10, 2) DEFAULT NULL, lieu VARCHAR(255) DEFAULT NULL, date_publication DATETIME NOT NULL, statut VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (idevent INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, date_evenement DATE NOT NULL, lieu VARCHAR(255) NOT NULL, nombre_billets INT NOT NULL, image VARCHAR(255) DEFAULT NULL, timestart TIME NOT NULL, event_mission LONGTEXT DEFAULT NULL, donation_objective DOUBLE PRECISION DEFAULT NULL, seatprice DOUBLE PRECISION NOT NULL, PRIMARY KEY(idevent)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE formation (formation_id INT AUTO_INCREMENT NOT NULL, categorie_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, nbrpart INT NOT NULL, prix DOUBLE PRECISION NOT NULL, date_creation DATE NOT NULL, INDEX IDX_404021BFBCF5E72D (categorie_id), PRIMARY KEY(formation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE galerie (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, datecreation DATE NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_9E7D1590A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inscription (inscription_id INT AUTO_INCREMENT NOT NULL, formation_id INT DEFAULT NULL, user_id INT DEFAULT NULL, date_inscription DATE NOT NULL, statut VARCHAR(10) NOT NULL, date_creation DATE NOT NULL, INDEX IDX_5E90F6D65200282E (formation_id), INDEX IDX_5E90F6D6A76ED395 (user_id), PRIMARY KEY(inscription_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, auteur_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, date_pub DATE NOT NULL, INDEX IDX_B6BD307F60BB6FE6 (auteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponse (id INT AUTO_INCREMENT NOT NULL, auteur_id INT DEFAULT NULL, message_id INT DEFAULT NULL, contenu LONGTEXT NOT NULL, date_pub DATE NOT NULL, INDEX IDX_5FB6DEC760BB6FE6 (auteur_id), INDEX IDX_5FB6DEC7537A1329 (message_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id_reservation INT AUTO_INCREMENT NOT NULL, userid INT DEFAULT NULL, idevent INT DEFAULT NULL, reservation_date DATE NOT NULL, seats_reserved INT NOT NULL, total_amount DOUBLE PRECISION NOT NULL, INDEX IDX_42C84955F132696E (userid), INDEX IDX_42C84955EDAB66BE (idevent), PRIMARY KEY(id_reservation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (user_id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, mdps VARCHAR(255) NOT NULL, numtlf VARCHAR(20) DEFAULT NULL, age INT DEFAULT NULL, role VARCHAR(10) NOT NULL, photo_de_profile VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66825396CB FOREIGN KEY (galerie_id) REFERENCES galerie (id)');
        $this->addSql('ALTER TABLE candidature ADD CONSTRAINT FK_E33BD3B86AB5B471 FOREIGN KEY (candidat) REFERENCES `user` (user_id)');
        $this->addSql('ALTER TABLE candidature_offre ADD CONSTRAINT FK_91FCEF3BB6121583 FOREIGN KEY (candidature_id) REFERENCES candidature (id)');
        $this->addSql('ALTER TABLE candidature_offre ADD CONSTRAINT FK_91FCEF3BEC013E12 FOREIGN KEY (emploi_id) REFERENCES emploi (id)');
        $this->addSql('ALTER TABLE donation ADD CONSTRAINT FK_31E581A0EDAB66BE FOREIGN KEY (idevent) REFERENCES event (idevent)');
        $this->addSql('ALTER TABLE donation ADD CONSTRAINT FK_31E581A0F132696E FOREIGN KEY (userid) REFERENCES `user` (user_id)');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BFBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (categorie_id)');
        $this->addSql('ALTER TABLE galerie ADD CONSTRAINT FK_9E7D1590A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (user_id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D65200282E FOREIGN KEY (formation_id) REFERENCES formation (formation_id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (user_id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F60BB6FE6 FOREIGN KEY (auteur_id) REFERENCES `user` (user_id)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC760BB6FE6 FOREIGN KEY (auteur_id) REFERENCES `user` (user_id)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC7537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955F132696E FOREIGN KEY (userid) REFERENCES `user` (user_id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955EDAB66BE FOREIGN KEY (idevent) REFERENCES event (idevent)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66825396CB');
        $this->addSql('ALTER TABLE candidature DROP FOREIGN KEY FK_E33BD3B86AB5B471');
        $this->addSql('ALTER TABLE candidature_offre DROP FOREIGN KEY FK_91FCEF3BB6121583');
        $this->addSql('ALTER TABLE candidature_offre DROP FOREIGN KEY FK_91FCEF3BEC013E12');
        $this->addSql('ALTER TABLE donation DROP FOREIGN KEY FK_31E581A0EDAB66BE');
        $this->addSql('ALTER TABLE donation DROP FOREIGN KEY FK_31E581A0F132696E');
        $this->addSql('ALTER TABLE formation DROP FOREIGN KEY FK_404021BFBCF5E72D');
        $this->addSql('ALTER TABLE galerie DROP FOREIGN KEY FK_9E7D1590A76ED395');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D65200282E');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6A76ED395');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F60BB6FE6');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC760BB6FE6');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC7537A1329');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955F132696E');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955EDAB66BE');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE candidature');
        $this->addSql('DROP TABLE candidature_offre');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE donation');
        $this->addSql('DROP TABLE emploi');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE formation');
        $this->addSql('DROP TABLE galerie');
        $this->addSql('DROP TABLE inscription');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE reponse');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
