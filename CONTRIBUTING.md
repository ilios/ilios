# Contribute to Ilios

So you want to help out with Ilios development?  **That's Fantastic**.  Here is
how you can get started:

## Overview of the process

1. [Initial Setup](#initial-setup)
2. Create a branch for your work
3. [Make some changes](#writing-code-for-ilios)
4. Run tests to ensure your changes haven't adversely affected other areas of the system.
5. Commit your changes with a good [Commit Message](#commit-message)
5. Push your feature branch to your fork of Ilios on Github
6. [Create a pull request](#create-a-pull-request-on-github) so we can review, discuss, and merge your changes.

### Initial Setup

1. Make sure you have a [GitHub account](https://github.com/signup/free)
2. Fork the repository in GitHub with the 'Fork' button

### Writing code for Ilios

In order to get your changes accepted you will need do:

 * Follow the [php coding standards](http://www.php-fig.org/)
 * Write tests which cover your changes

### Running Tests
1. vendor/bin/phpunit
2. vendor/bin/phpcs --standard=app/phpcs.xml src

### Commit Message

Some good rules for commit messages are

 * the first line is commit summary, 50 characters or less,
 * followed by an empty line
 * followed by a longer explanation of the commit if necessary

The first line of a commit message becomes the **title** of a pull
request on GitHub, like the subject line of an email.  Including
the key info in the first line will help us respond faster to
your pull.

### Create a Pull Request on Github

Go to *your* GitHub repository at
https://github.com/my-github-username/ilios, switch branch to your
topic branch and click the 'Pull Request' button. You can then add further
comments to your pull request.
