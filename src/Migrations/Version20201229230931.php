<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201229230931 extends AbstractMigration
{
  public function getDescription(): string
  {
      return 'Creates user, image and newsArticle tables';
  }

  public function up(Schema $schema): void
  {
    $this->abortIf(
      $this->connection->getDatabasePlatform()->getName() !== 'mysql',
      'Migration can only be executed safely on \'mysql\'.'
    );

    $this->addSql(
        'CREATE TABLE image
        (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT DEFAULT NULL,
            created_at DATETIME(6) NOT NULL,
            updated_at DATETIME(6) NOT NULL,
            filename VARCHAR(255) NOT NULL,
            path VARCHAR(255) NOT NULL,
            type VARCHAR(255) NOT NULL,
            alt VARCHAR(255) DEFAULT NULL,
            title VARCHAR(255) DEFAULT NULL,
            INDEX IDX_C53D045FA76ED395 (user_id),
            PRIMARY KEY(id)
        )
        DEFAULT CHARACTER SET utf8mb4
        COLLATE `utf8mb4_unicode_ci`
        ENGINE = InnoDB'
    );

    $this->addSql(
        'CREATE TABLE news_article
        (
            id INT AUTO_INCREMENT NOT NULL,
            author_id INT NOT NULL,
            created_at DATETIME(6) NOT NULL,
            updated_at DATETIME(6) NOT NULL,
            title VARCHAR(255) NOT NULL,
            text LONGTEXT NOT NULL,
            UNIQUE INDEX UNIQ_55DE12802B36786B (title),
            INDEX IDX_55DE1280F675F31B (author_id),
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
            created_at DATETIME(6) NOT NULL,
            updated_at DATETIME(6) NOT NULL,
            username VARCHAR(255) NOT NULL,
            roles JSON NOT NULL,
            password VARCHAR(255) NOT NULL,
            theme VARCHAR(255) NOT NULL,
            color VARCHAR(255) NOT NULL,
            UNIQUE INDEX UNIQ_8D93D649F85E0677 (username),
            UNIQUE INDEX UNIQ_8D93D6493DA5256D (image_id),
            PRIMARY KEY(id)
        )
        DEFAULT CHARACTER SET utf8mb4
        COLLATE `utf8mb4_unicode_ci`
        ENGINE = InnoDB'
    );

    $this->addSql(
        'ALTER TABLE image
        ADD CONSTRAINT FK_C53D045FA76ED395
        FOREIGN KEY (user_id)
        REFERENCES user (id)'
    );

    $this->addSql(
        'ALTER TABLE news_article
        ADD CONSTRAINT FK_55DE1280F675F31B
        FOREIGN KEY (author_id)
        REFERENCES user (id)'
    );

    $this->addSql(
        'ALTER TABLE user
        ADD CONSTRAINT FK_8D93D6493DA5256D
        FOREIGN KEY (image_id)
        REFERENCES image (id)'
    );
  }

  public function down(Schema $schema): void
  {
    $this->abortIf(
      $this->connection->getDatabasePlatform()->getName() !== 'mysql',
      'Migration can only be executed safely on \'mysql\'.'
    );

    $this->addSql(
        'ALTER TABLE image
        DROP FOREIGN KEY FK_C53D045FA76ED395'
    );

    $this->addSql(
        'ALTER TABLE news_article
        DROP FOREIGN KEY FK_55DE1280F675F31B'
    );

    $this->addSql(
        'ALTER TABLE user
        DROP FOREIGN KEY FK_8D93D6493DA5256D'
    );

    $this->addSql('DROP TABLE image');
    $this->addSql('DROP TABLE news_article');
    $this->addSql('DROP TABLE user');
  }
}
