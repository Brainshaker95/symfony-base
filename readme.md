# Symfony Base

## Table of contents

- [Prerequisites](#prerequisites)
- [Installing](#installing)
- [Commands](#commands)

___

## Prerequisites

  * [Node.js](https://nodejs.org/)
  * [Yarn](https://yarnpkg.com/)
  * [Composer](https://getcomposer.org/)

___

## Installing

```shell
  make install
```
or without make

```shell
  yarn && composer install
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
```

&nbsp;

With make

```makefile
  cache-clear: // clear symfony cache

  install: // install dependencies

  migrate: // migrate database

  migrate-first: // revert database

  phpstan: // execute php analysis

  webpack: // build assets dev

  webpack-p: // build assets prod

  webpack-watch: // watch assets
```
