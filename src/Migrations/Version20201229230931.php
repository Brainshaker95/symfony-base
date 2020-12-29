<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201229230931 extends AbstractMigration
{
  public function getDescription(): string
  {
      return 'Creates app_user, image and newsArticle tables';
  }

  public function up(Schema $schema): void
  {
    $databasePlattformName = $this->connection->getDatabasePlatform()->getName();

    $this->abortIf(
      $databasePlattformName !== 'mysql' && $databasePlattformName !== 'postgresql',
      'Migration can only be executed safely on \'mysql\' or \'mysql\'.'
    );

    if ($databasePlattformName === 'mysql') {
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
        'CREATE TABLE app_user
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
        REFERENCES app_user (id)'
      );

      $this->addSql(
        'ALTER TABLE news_article
        ADD CONSTRAINT FK_55DE1280F675F31B
        FOREIGN KEY (author_id)
        REFERENCES app_user (id)'
      );

      $this->addSql(
        'ALTER TABLE app_user
        ADD CONSTRAINT FK_8D93D6493DA5256D
        FOREIGN KEY (image_id)
        REFERENCES image (id)'
      );
    } else if ($databasePlattformName === 'postgresql') {
      $this->addSql(
        'CREATE TABLE app_user
        (
          id INT NOT NULL,
          image_id INT DEFAULT NULL,
          created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL,
          updated_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL,
          username VARCHAR(255) NOT NULL,
          roles TEXT NOT NULL,
          password VARCHAR(255) NOT NULL,
          theme VARCHAR(255) NOT NULL,
          color VARCHAR(255) NOT NULL,
          PRIMARY KEY(id)
        )'
      );

      $this->addSql(
        'CREATE UNIQUE INDEX UNIQ_88BDF3E9F85E0677
        ON app_user (username)'
      );

      $this->addSql(
        'CREATE UNIQUE INDEX UNIQ_88BDF3E93DA5256D
        ON app_user (image_id)'
      );

      $this->addSql('COMMENT ON COLUMN app_user.roles IS \'(DC2Type:json)\'');

      $this->addSql(
        'CREATE TABLE image
        (
          id INT NOT NULL,
          user_id INT DEFAULT NULL,
          created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL,
          updated_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL,
          filename VARCHAR(255) NOT NULL,
          path VARCHAR(255) NOT NULL,
          type VARCHAR(255) NOT NULL,
          alt VARCHAR(255) DEFAULT NULL,
          title VARCHAR(255) DEFAULT NULL,
          PRIMARY KEY(id)
        )'
      );

      $this->addSql(
        'CREATE INDEX IDX_C53D045FA76ED395
        ON image (user_id)'
      );

      $this->addSql(
        'CREATE TABLE news_article
        (
          id INT NOT NULL,
          author_id INT NOT NULL,
          created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL,
          updated_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL,
          title VARCHAR(255) NOT NULL,
          text TEXT NOT NULL,
          PRIMARY KEY(id)
        )'
      );

      $this->addSql(
        'CREATE UNIQUE INDEX UNIQ_55DE12802B36786B
        ON news_article (title)'
      );

      $this->addSql(
        'CREATE INDEX IDX_55DE1280F675F31B
        ON news_article (author_id)'
      );

      $this->addSql(
        'ALTER TABLE app_user
        ADD CONSTRAINT FK_88BDF3E93DA5256D
        FOREIGN KEY (image_id)
        REFERENCES image (id)
        NOT DEFERRABLE
        INITIALLY
        IMMEDIATE'
      );

      $this->addSql(
        'ALTER TABLE image
        ADD CONSTRAINT FK_C53D045FA76ED395
        FOREIGN KEY (user_id)
        REFERENCES app_user (id)
        NOT DEFERRABLE
        INITIALLY
        IMMEDIATE'
      );

      $this->addSql(
        'ALTER TABLE news_article
        ADD CONSTRAINT FK_55DE1280F675F31B
        FOREIGN KEY (author_id)
        REFERENCES app_user (id)
        NOT DEFERRABLE
        INITIALLY
        IMMEDIATE'
      );
    }
  }

  public function down(Schema $schema): void
  {
    $databasePlattformName = $this->connection->getDatabasePlatform()->getName();

    $this->abortIf(
      $databasePlattformName !== 'mysql' && $databasePlattformName !== 'postgresql',
      'Migration can only be executed safely on \'mysql\' or \'mysql\'.'
    );

    if ($databasePlattformName === 'mysql') {
      $this->addSql(
        'ALTER TABLE image
        DROP FOREIGN KEY FK_C53D045FA76ED395'
      );

      $this->addSql(
        'ALTER TABLE news_article
        DROP FOREIGN KEY FK_55DE1280F675F31B'
      );

      $this->addSql(
        'ALTER TABLE app_user
        DROP FOREIGN KEY FK_8D93D6493DA5256D'
      );

      $this->addSql('DROP TABLE image');
      $this->addSql('DROP TABLE news_article');
      $this->addSql('DROP TABLE app_user');
    } else if ($databasePlattformName === 'postgresql') {
      $this->addSql('CREATE SCHEMA IF NOT EXISTS public');

      $this->addSql(
        'ALTER TABLE image
        DROP CONSTRAINT FK_C53D045FA76ED395'
      );

      $this->addSql(
        'ALTER TABLE news_article
        DROP CONSTRAINT FK_55DE1280F675F31B'
      );

      $this->addSql(
        'ALTER TABLE app_user
        DROP CONSTRAINT FK_88BDF3E93DA5256D'
      );

      $this->addSql('DROP TABLE app_user');
      $this->addSql('DROP TABLE image');
      $this->addSql('DROP TABLE news_article');
    }
  }
}
