<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250211145050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE donation CHANGE idevent idevent INT NOT NULL');
        $this->addSql('ALTER TABLE event ADD dateEvenement DATETIME NOT NULL, DROP date_evenement, CHANGE nombre_billets nombreBillets INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE donation CHANGE idevent idevent INT DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD date_evenement DATE NOT NULL, DROP dateEvenement, CHANGE nombreBillets nombre_billets INT NOT NULL');
    }
}
