<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250224230147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add real bad words to filter';
    }

    public function up(Schema $schema): void
    {
        // Supprimer les anciens mots de test
        $this->addSql('DELETE FROM bad_word WHERE word IN ("badword1", "badword2")');

        // Ajouter les vrais mots à filtrer
        $this->addSql("INSERT INTO bad_word (word, replacement) VALUES 
            ('merde', '****'),
            ('putain', '*****'),
            ('connard', '*******'),
            ('pute', '****'),
            ('salope', '******'),
            ('enculé', '******'),
            ('fuck', '****')
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM bad_word WHERE word IN ("merde", "putain", "connard", "pute", "salope", "enculé", "fuck")');
        $this->addSql("INSERT INTO bad_word (word, replacement) VALUES ('badword1', '****'), ('badword2', '****')");
    }
} 