# Symfony Base

## Table of contents

- [Prerequisites](#prerequisites)
- [Installing](#installing)
- [Configuration](#configuration)
- [Commands](#commands)

___

## Prerequisites

  * [Node.js](https://nodejs.org/)
  * [Yarn](https://yarnpkg.com/)
  * [Composer](https://getcomposer.org/)

___

## Installing

```shell
  yarn && composer install
```
With make

```shell
  make install
```

___

## Configuration

```shell
  cp .env.example .env
```

Configure to your needs

___

## Commands

```shell
  # build assets dev
  yarn dev

  # build assets prod
  yarn prod

  # watch assets
  yarn watch
```

&nbsp;

With make

```makefile
  cache-clear: // clear symfony cache

  entity: // create doctrine enitity

  install: // install dependencies

  migrate: // migrate database

  migrate-first: // revert database

  migration: // create migration

  phpstan: // execute php analysis

  webpack: // build assets dev

  webpack-p: // build assets prod

  webpack-watch: // watch assets
```
