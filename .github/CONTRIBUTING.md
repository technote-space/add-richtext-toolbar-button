# Contributing

[issues]: /issues
[fork]: /fork
[pr]: /compare
[js-style]: https://standardjs.com/
[eslint]: https://eslint.org/
[jest]: https://jestjs.io/
[mocha]: https://mochajs.org/
[phpcs]: https://github.com/squizlabs/PHP_CodeSniffer/wiki/Usage
[phpmd]: https://phpmd.org/documentation/index.html
[wp-test]: https://developer.wordpress.org/cli/commands/scaffold/plugin-tests/
[code-of-conduct]: CODE_OF_CONDUCT.md

When contributing to this repository, please first discuss the change you wish to make via [issue][issues] with the owners of this repository before making a change. 

Please note we have a [Contributor Code of Conduct][code-of-conduct], please follow it in all your interactions with the project.

## Submitting a pull request

1. [Fork][fork] and clone the repository
1. Configure and install the dependencies:
   - `composer setup`  # install and build
   - `composer bin:download`  # download test scripts
   - `composer bin:prepare`   # prepare test settings and download dependent plugins
1. Make sure the tests pass on your machine: `composer bin:test-p`, which contains
   - [`PHPCS`][phpcs]
   - [`PHPMD`][phpmd]
   - [`WordPress Plugin Tests`][wp-test]
   - [`ESLint`][eslint]
   - [`Jest`][jest] or [`Mocha`][mocha]
   - `Build test`
1. Create a new branch: `git checkout -b my-branch-name`
1. Make your change, add tests, and make sure the tests still pass.
1. Push to your fork and [submit a pull request][pr].
1. Pat your self on the back and wait for your pull request to be reviewed and merged.

Here are a few things you can do that will increase the likelihood of your pull request being accepted:
- Follow the style guides ([JavaScript Standard Style][js-style], [PHPCS][phpcs], [PHPMD][phpmd]). Any linting errors should be shown when running 
  - `composer bin:js-lint`
  - `composer bin:phpcs`
  - `composer bin:phpmd`
- Write and update tests.
- Keep your change as focused as possible. If there are multiple changes you would like to make that are not dependent upon each other, consider submitting them as separate pull requests.
- Write a [good commit message](https://github.com/erlang/otp/wiki/writing-good-commit-messages).

Work in Progress pull request are also welcome to get feedback early on, or if there is something blocked you.

## Resources

- [How to Contribute to Open Source](https://opensource.guide/how-to-contribute/)
- [Using Pull Requests](https://help.github.com/articles/about-pull-requests/)
- [GitHub Help](https://help.github.com)
