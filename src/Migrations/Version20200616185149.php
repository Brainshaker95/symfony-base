<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200616185149 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Creats user and image tables';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
            'CREATE TABLE image
            (
                id INT AUTO_INCREMENT NOT NULL,
                filename VARCHAR(255) NOT NULL,
                path VARCHAR(255) NOT NULL,
                type VARCHAR(255) DEFAULT NULL,
                PRIMARY KEY(id)
            )
            DEFAULT CHARACTER SET utf8mb4
            COLLATE `utf8mb4_unicode_ci`
            ENGINE = InnoDB'
        );

        $this->addSql(
            'CREATE TABLE user
            (
                id INT AUTO_INCREMENT NOT NULL,
                image_id INT DEFAULT NULL,
                username VARCHAR(180) NOT NULL,
                roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\',
                password VARCHAR(255) NOT NULL,
                UNIQUE INDEX UNIQ_8D93D649F85E0677 (username),
                UNIQUE INDEX UNIQ_8D93D6493DA5256D (image_id),
                PRIMARY KEY(id)
            )
            DEFAULT CHARACTER SET utf8mb4
            COLLATE `utf8mb4_unicode_ci`
            ENGINE = InnoDB'
        );

        $this->addSql(
            'ALTER TABLE user
            ADD CONSTRAINT FK_8D93D6493DA5256D
            FOREIGN KEY (image_id)
            REFERENCES image (id)'
        );
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
            'ALTER TABLE user
            DROP FOREIGN KEY FK_8D93D6493DA5256D'
        );

        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE user');
    }
}
