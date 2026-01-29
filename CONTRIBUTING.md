# Contributing to Kirby Language Release

Thank you for your interest in contributing! This document provides guidelines for contributing to this project.

## Code of Conduct

Be respectful and inclusive. We're all here to make this plugin better.

## How to Contribute

### Reporting Bugs

Before creating a bug report, please check existing issues to avoid duplicates.

When creating a bug report, include:
- Plugin version
- Kirby CMS version
- PHP version
- Steps to reproduce
- Expected behavior
- Actual behavior
- Screenshots (if applicable)

### Suggesting Features

Feature suggestions are welcome! Please:
- Check if the feature already exists or is planned
- Explain the use case
- Provide examples of how it would work

### Pull Requests

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Test thoroughly
5. Commit your changes (`git commit -m 'Add amazing feature'`)
6. Push to the branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

### Development Setup

```bash
# Clone the repository
git clone https://github.com/nerdcel/kirby-languagerelease.git
cd kirby-languagerelease

# Install in a Kirby installation for testing
ln -s $(pwd) /path/to/kirby/site/plugins/languagerelease
```

### Coding Standards

- Follow PSR-12 coding standards for PHP
- Use meaningful variable and function names
- Comment complex logic
- Keep functions focused and small

### Testing

Before submitting a PR:
- Test with multiple languages
- Test all three behavior options (404, redirect, default-content)
- Test preview mode with valid and invalid tokens
- Test in Panel and frontend
- Verify translations display correctly

### Adding Translations

To add a new language:

1. Create `/languages/[lang-code].php`
2. Copy structure from existing language file
3. Translate all keys
4. Add language to the list in `index.php`
5. Update README.md with the new language

### Commit Messages

Use clear commit messages:
- `feat: Add new feature`
- `fix: Fix bug in preview mode`
- `docs: Update README`
- `style: Format code`
- `refactor: Improve performance`
- `test: Add tests`

## Questions?

Open a [Discussion](https://github.com/nerdcel/kirby-languagerelease/discussions) if you have questions.

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

