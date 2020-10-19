# Symfony Base

## Table of contents

- [Prerequisites](#prerequisites)
- [Installing](#installing)
- [Commands](#commands)
- [Documentation](./docs/0_index.md)

___

## Prerequisites

  * [Node.js](https://nodejs.org/)
  * [Yarn](https://yarnpkg.com/)
  * [Composer](https://getcomposer.org/)

  ### Optional Tools

  * [Make](http://gnuwin32.sourceforge.net/packages/make.htm)

___

## Installing

### Automatically

```shell
  (yarn|make) startup
```

### Manually

```shell
  yarn && composer install

  # or with make

  make vendor
```

Set up environment and parameters and configure to your needs

```shell
  cp .env.example .env && cp ./config/parameters.yml.example ./config/parameters.yml
```
___

## Commands

```shell
  # build assets dev
  yarn dev

  # build assets prod
  yarn prod

  # watch assets
  yarn watch

  # initialize project
  yarn startup
```

&nbsp;

With make

```makefile  
  cache-clear: ## clear symfony cache

  clear-thumbs: ## clear liip imagine cache

  entity: ## create doctrine enitity

  help: ## show this help

  install: ## install dependencies

  migrate: ## migrate database

  migrate-first: ## revert database

  migrate-next: ## migrate database to next version
  
  migrate-prev: ## migrate database to previous version

  migration: ## create migration

  phpstan: ## execute php analysis

  startup: ## initialize project

  webpack: ## build assets dev

  webpack-p: ## build assets prod

  webpack-watch: ## watch assets
```
