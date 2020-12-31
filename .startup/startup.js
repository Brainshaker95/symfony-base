const chalk = require('chalk');
const dotenv = require('dotenv');
const { exec } = require('child_process');
const fs = require('fs');
const inquirer = require('inquirer');
const path = require('path');
const yaml = require('js-yaml');

dotenv.config();

const logo = '~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n\n'
+ '   _____                 ____                     ____\n'
+ '  / ___/__  ______ ___  / __/___  ____  __  __   / __ )____  ____ ___\n'
+ '  \\__ \\/ / / / __ `__ \\/ /_/ __ \\/ __ \\/ / / /  / __  / __ `/ ___/ _ \\\n'
+ ' ___/ / /_/ / / / / / / __/ /_/ / / / / /_/ /  / /_/ / /_/ (__  )  __/\n'
+ '/____/\\__, /_/ /_/ /_/_/  \\____/_/ /_/\\__, /  /_____/\\__,_/____/\\___/\n'
+ '     /____/                          /____/\n\n'
+ '~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n\n';

const dotenvPath = path.resolve(__dirname, '../.env');
const exampleDotenvPath = path.resolve(__dirname, '../.env.example');
const parametersPath = path.resolve(__dirname, '../config/parameters.yml');
const exampleParametersPath = path.resolve(__dirname, '../config/parameters.yml.example');
const alphanumerics = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
const exampleDotenv = dotenv.config({ path: exampleDotenvPath }).parsed;
let exampleParameters = {};
let parameters = {};
const devProd = ['dev', 'prod'];
const yesNo = ['y', 'n'];

const dotenvDefaults = {
  baseUrl: 'symfony-base.local',
  env: 'dev',
  databaseName: 'symfony-base',
  databaseUser: 'root',
  databasePassword: 'password',
  databaseHost: 'localhost',
  databasePort: 3306,
  databaseServerVersion: '5.7',
  databaseDumpPath: 'data/dump.sql',
  databaseDumpKey: 'changeThisDumpKey',
  phpstanLevel: 8,
};

const parametersDefaults = {
  hashidsSalt: 'TODO_AddHashSalt',
  hashidsPadding: 8,
  uploadDirectory: '%kernel.project_dir%/public/uploads',
};

try {
  parameters = yaml.safeLoad(fs.readFileSync(parametersPath)).parameters;
} catch (e) {
  //
}

try {
  exapmpleParameters = yaml.safeLoad(fs.readFileSync(exampleParametersPath)).parameters;
} catch (e) {
  //
}

const createDotenv = (answers) => answers.createDotenv === 'y';
const createParameters = (answers) => answers.createParameters === 'y';

const generateRandomString = (length) => Array
  .from({ length }, () => alphanumerics[Math.floor(Math.random() * alphanumerics.length)])
  .join('');

const getDotenvString = (answers) => `
APP_ENV=${answers.env || dotenvDefaults.env}
APP_SECRET=${generateRandomString(32)}
BASE=${answers.baseUrl || dotenvDefaults.baseUrl}
DATABASE_NAME=${answers.databaseName || dotenvDefaults.databaseName}
DATABASE_USER=${answers.databaseUser || dotenvDefaults.databaseUser}
DATABASE_PASSWORD=${answers.databasePassword || dotenvDefaults.databasePassword}
DATABASE_HOST=${answers.databaseHost || dotenvDefaults.databaseHost}
DATABASE_PORT=${answers.databasePort || dotenvDefaults.databasePort}
DATABASE_SERVER_VERSION=${answers.databaseServerVersion || dotenvDefaults.databaseServerVersion}
DATABASE_DUMP_PATH=${answers.databaseDumpPath || dotenvDefaults.databaseDumpPath}
DATABASE_DUMP_KEY=${answers.databaseDumpKey || dotenvDefaults.databaseDumpKey}
PHPSTAN_LEVEL=${answers.phpstanLevel || dotenvDefaults.phpstanLevel}
REDIRECT_STATUS=301
`.trim();

const getParametersString = (answers) => `
parameters:
  hashids_salt: ${answers.hashidsSalt || parametersDefaults.hashidsSalt}
  hashids_padding: ${answers.hashidsPadding || parametersDefaults.hashidsPadding}
  upload_directory: \'${answers.uploadDirectory || parametersDefaults.uploadDirectory}\'
  accent_colors:
    color.blue: 'default'
    color.burgundy: '#771004'
    color.green: '#3b6533'
    color.purple: 'rebeccapurple'
    color.orange: '#bf8419'
    color.pink: '#b618f8'
    color.red: '#b60000'
`.trim();

const questions = [{
  name: 'createDotenv',
  default: 'y',
  choices: yesNo,
  message: `${chalk.cyan.underline('Create .env?')} ${chalk.yellow.bold('[y/n]')}`,
  validate: (input) => yesNo.indexOf(input) > -1,
}, {
  when: createDotenv,
  name: 'env',
  default: process.env.APP_ENV || exampleDotenv.APP_ENV || dotenvDefaults.env,
  choices: devProd,
  message: `${chalk.cyan.underline('App environment')} ${chalk.yellow.bold('[dev/prod]')}`,
  validate: (input) => devProd.indexOf(input) > -1,
}, {
  when: createDotenv,
  name: 'baseUrl',
  default: process.env.BASE || exampleDotenv.BASE || dotenvDefaults.baseUrl,
  message: chalk.cyan.underline('Base url'),
}, {
  when: createDotenv,
  name: 'databaseName',
  default: process.env.DATABASE_NAME
    || exampleDotenv.DATABASE_NAME
    || dotenvDefaults.databaseName,
  message: chalk.cyan.underline('Database name'),
}, {
  when: createDotenv,
  name: 'databaseUser',
  default: process.env.DATABASE_USER
    || exampleDotenv.DATABASE_USER
    || dotenvDefaults.databaseUser,
  message: chalk.cyan.underline('Database user'),
}, {
  when: createDotenv,
  name: 'databasePassword',
  default: process.env.DATABASE_PASWSWORD
    || exampleDotenv.DATABASE_PASWSWORD
    || dotenvDefaults.databasePassword,
  message: chalk.cyan.underline('Database password'),
}, {
  when: createDotenv,
  name: 'databaseHost',
  default: process.env.DATABASE_HOST
    || exampleDotenv.DATABASE_HOST
    || dotenvDefaults.databaseHost,
  message: chalk.cyan.underline('Database host'),
}, {
  when: createDotenv,
  name: 'databasePort',
  default: process.env.DATABASE_PORT
    || exampleDotenv.DATABASE_PORT
    || dotenvDefaults.databasePort,
  message: chalk.cyan.underline('Database port'),
}, {
  when: createDotenv,
  name: 'databaseServerVersion',
  default: process.env.DATABASE_SERVER_VERSION
    || exampleDotenv.DATABASE_SERVER_VERSION
    || dotenvDefaults.databaseServerVersion,
  message: chalk.cyan.underline('Database server version'),
}, {
  when: createDotenv,
  name: 'databaseDumpPath',
  default: process.env.DATABASE_DUMP_PATH
    || exampleDotenv.DATABASE_DUMP_PATH
    || dotenvDefaults.databaseDumpPath,
  message: chalk.cyan.underline('Database dump path'),
}, {
  when: createDotenv,
  name: 'databaseDumpKey',
  default: process.env.DATABASE_DUMP_KEY
    || exampleDotenv.DATABASE_DUMP_KEY
    || dotenvDefaults.databaseDumpKey,
  message: chalk.cyan.underline('Database dump key'),
}, {
  when: createDotenv,
  name: 'phpstanLevel',
  default: process.env.PHPSTAN_LEVEL
    || exampleDotenv.PHPSTAN_LEVEL
    || dotenvDefaults.phpstanLevel,
  message: chalk.cyan.underline('PHPStan level'),
}, {
  name: 'createParameters',
  default: 'y',
  choices: yesNo,
  message: `${chalk.cyan.underline('Create parameters.yml?')} ${chalk.yellow.bold('[y/n]')}`,
  validate: (input) => yesNo.indexOf(input) > -1,
}, {
  when: createParameters,
  name: 'hashidsSalt',
  default: parameters.hashids_salt
    || exampleParameters.hashids_salt
    || parametersDefaults.hashidsSalt,
  message: chalk.cyan.underline('Hashids salt'),
}, {
  when: createParameters,
  name: 'hashidsPadding',
  default: parameters.hashids_padding
    || exampleParameters.hashids_padding
    || parametersDefaults.hashidsPadding,
  message: chalk.cyan.underline('Hashids padding'),
}, {
  when: createParameters,
  name: 'uploadDirectory',
  default: parameters.upload_directory
    || exampleParameters.upload_directory
    || parametersDefaults.uploadDirectory,
  message: chalk.cyan.underline('Upload directory'),
}];

const installProject = (answers) => {
  console.log(chalk.green.bold('\n~~~~~~~~~~~~~~~ Composer ~~~~~~~~~~~~~~~\n'));

  exec('composer install', (error, stdout) => {
    if (error) {
      throw error;
    }

    console.log(chalk.cyan(stdout));
    console.log(chalk.green.bold('\n~~~~~~~~~~~~~~~~~ Yarn ~~~~~~~~~~~~~~~~~\n'));

    exec('yarn', (error, stdout) => {
      if (error) {
        throw error;
      }

      console.log(chalk.cyan(stdout));
      console.log(chalk.green.bold('\n~~~~~~~~~~~~~~~~ Migrations ~~~~~~~~~~~~~~~~\n'));

      exec('php bin/console doctrine:migrations:migrate --no-interaction', (error, stdout) => {
        if (error) {
          console.log(chalk.red(stdout));
        } else {
          console.log(chalk.cyan(stdout));
        }

        console.log(chalk.green.bold('\n~~~~~~~~~~~~~~~~ Encore ~~~~~~~~~~~~~~~~\n'));

        exec(`yarn encore ${answers.env || dotenvDefaults.env}`, (error, stdout) => {
          if (error) {
            throw error;
          }

          console.log(chalk.cyan(stdout));
          console.log(chalk.black.bgGreenBright.bold('\n\n'
          + '                                             \n'
          + '       Startup successfully completed!       \n'
          + '                                             '));
        });
      });
    });
  });
};

const writeParameters = (answers) => {
  fs.writeFile(parametersPath, getParametersString(answers), (error) => {
    if (error) {
      console.log(chalk.black.bgRed.bold('\n\n'
      + '                                             \n'
      + '     Error writing parameters.yml file!      \n'
      + '                                             '));

      throw error;
    }

    installProject(answers);
  });
};

const handleAnswers = (answers) => {
  const doCreateDotenv = createDotenv(answers);
  const doCreateParameters = createParameters(answers);

  if (!doCreateDotenv && !doCreateParameters) {
    installProject(answers);

    return;
  }

  if (!doCreateDotenv) {
    writeParameters(answers);

    return;
  }

  fs.writeFile(dotenvPath, getDotenvString(answers), (error) => {
    if (error) {
      console.log(chalk.black.bgRed.bold('\n\n'
      + '                                             \n'
      + '          Error writing .env file!           \n'
      + '                                             '));

      throw error;
    }

    if (doCreateParameters) {
      writeParameters(answers);
    } else {
      installProject(answers);
    }
  });
};

console.log(chalk.green.bold(logo));

inquirer
  .prompt(questions)
  .then(handleAnswers)
  .catch((error) => {
    console.log(chalk.black.bgRed.bold('\n\n'
    + '                                             \n'
    + '             Error during setup!             \n'
    + '                                             '));

    throw error;
  });
