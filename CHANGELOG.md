# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.2] 2020-07-26

### Fixed

- Running `php artisan seed:reset` to undo all seeds, or `php artisan seed:rollback`, to undo the last seeds will correctly undo them in reverse order (read: the most recent first).

## [0.1.1] 2020-07-25

### Fixed

- Laravel versions 5.5+ will no longer have an issue when running `php artisan seed:make` (which was caused when creating the seeders table).
