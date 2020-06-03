<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200603080815 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category CHANGE eid eid INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product CHANGE eid eid INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_57698A6A3EE4B093 ON role (short_name)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category CHANGE eid eid INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product CHANGE eid eid INT DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_57698A6A3EE4B093 ON role');
    }
}
